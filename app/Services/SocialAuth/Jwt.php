<?php

namespace App\Services\SocialAuth;

class Jwt
{
    public static function payload(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3 || $parts[1] === '') {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        $json = self::base64UrlDecode($parts[1]);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        return $data;
    }

    public static function signEs256(array $header, array $payload, string $privateKeyPem): string
    {
        $header['alg'] = 'ES256';
        $segments = [
            self::base64UrlEncode(json_encode($header)),
            self::base64UrlEncode(json_encode($payload)),
        ];
        $signingInput = implode('.', $segments);

        $key = openssl_pkey_get_private($privateKeyPem);
        if ($key === false) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        $der = '';
        $signed = openssl_sign($signingInput, $der, $key, OPENSSL_ALGO_SHA256);
        if (!$signed) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        $segments[] = self::base64UrlEncode(self::derToRaw($der, 32));

        return implode('.', $segments);
    }

    public static function derToRaw(string $der, int $partLength): string
    {
        $offset = 0;
        if (strlen($der) < 8 || ord($der[$offset++]) !== 0x30) {
            throw new \RuntimeException('Invalid DER signature');
        }

        self::readDerLength($der, $offset);

        $r = self::readDerInteger($der, $offset);
        $s = self::readDerInteger($der, $offset);

        return self::padPart($r, $partLength).self::padPart($s, $partLength);
    }

    public static function rawToDer(string $raw): string
    {
        $half = (int) (strlen($raw) / 2);
        $body = self::unsignedInteger(substr($raw, 0, $half)).self::unsignedInteger(substr($raw, $half));

        return "\x30".self::encodeLength(strlen($body)).$body;
    }

    private static function readDerInteger(string $der, int &$offset): string
    {
        if (!isset($der[$offset]) || ord($der[$offset++]) !== 0x02) {
            throw new \RuntimeException('Invalid DER integer');
        }

        $length = self::readDerLength($der, $offset);
        $bytes = substr($der, $offset, $length);
        $offset += $length;

        return $bytes;
    }

    private static function readDerLength(string $der, int &$offset): int
    {
        $length = ord($der[$offset++]);
        if (($length & 0x80) === 0) {
            return $length;
        }

        $count = $length & 0x1f;
        $length = 0;
        for ($i = 0; $i < $count; $i++) {
            $length = ($length << 8) | ord($der[$offset++]);
        }

        return $length;
    }

    private static function padPart(string $part, int $length): string
    {
        $part = ltrim($part, "\x00");
        if ($part === '') {
            $part = "\x00";
        }
        if (strlen($part) > $length) {
            throw new \RuntimeException('DER integer is too long');
        }

        return str_pad($part, $length, "\x00", STR_PAD_LEFT);
    }

    private static function unsignedInteger(string $bytes): string
    {
        $bytes = ltrim($bytes, "\x00");
        if ($bytes === '') {
            $bytes = "\x00";
        }
        if ((ord($bytes[0]) & 0x80) !== 0) {
            $bytes = "\x00".$bytes;
        }

        return "\x02".self::encodeLength(strlen($bytes)).$bytes;
    }

    private static function encodeLength(int $length): string
    {
        if ($length < 0x80) {
            return chr($length);
        }

        $encoded = '';
        while ($length > 0) {
            $encoded = chr($length & 0xff).$encoded;
            $length >>= 8;
        }

        return chr(0x80 | strlen($encoded)).$encoded;
    }

    public static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder > 0) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode(strtr($data, '-_', '+/'), true);
        if ($decoded === false) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        return $decoded;
    }
}
