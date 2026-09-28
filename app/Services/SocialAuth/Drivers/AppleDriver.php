<?php

namespace App\Services\SocialAuth\Drivers;

use App\Services\SocialAuth\Jwt;
use App\Services\SocialAuth\OAuthHttp;
use App\Services\SocialAuth\SocialAuthException;
use App\Services\SocialAuth\SocialIdentity;
use App\Services\SocialAuth\SocialLoginDriver;

class AppleDriver implements SocialLoginDriver
{
    /** @var OAuthHttp */
    private $http;

    /** @var string */
    private $clientId;

    /** @var string */
    private $redirectUrl;

    /** @var string */
    private $teamId;

    /** @var string */
    private $keyId;

    /** @var string */
    private $privateKey;

    public function __construct(
        OAuthHttp $http,
        string $clientId,
        string $redirectUrl,
        string $teamId,
        string $keyId,
        string $privateKey
    ) {
        $this->http = $http;
        $this->clientId = $clientId;
        $this->redirectUrl = $redirectUrl;
        $this->teamId = $teamId;
        $this->keyId = $keyId;
        $this->privateKey = $privateKey;
    }

    public function authorizationUrl(string $state): string
    {
        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'response_type' => 'code',
            'response_mode' => 'form_post',
            'scope' => 'name email',
            'state' => $state,
        ], '', '&', PHP_QUERY_RFC3986);

        return 'https://appleid.apple.com/auth/authorize?'.$query;
    }

    public function fetchIdentity(string $code, array $callback = []): SocialIdentity
    {
        $token = $this->http->postForm('https://appleid.apple.com/auth/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret(),
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUrl,
        ]);

        if (empty($token['id_token']) || !is_string($token['id_token'])) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        $claims = Jwt::payload($token['id_token']);
        $this->assertClaims($claims);

        $email = isset($claims['email']) && is_string($claims['email']) && $claims['email'] !== ''
            ? $claims['email']
            : null;

        // Apple only returns a verified address, and only on the first authorization.
        // An explicit false still blocks linking. A missing claim with an email is accepted.
        if (!array_key_exists('email_verified', $claims)) {
            $emailVerified = $email !== null;
        } else {
            $emailVerified = $this->isVerified($claims['email_verified']);
        }

        return new SocialIdentity(
            'apple',
            (string) $claims['sub'],
            $email,
            $emailVerified,
            $this->nameFromCallback($callback),
            null
        );
    }

    public function clientSecret(): string
    {
        $now = time();

        return Jwt::signEs256(
            ['kid' => $this->keyId, 'typ' => 'JWT'],
            [
                'iss' => $this->teamId,
                'iat' => $now,
                'exp' => $now + (86400 * 150),
                'aud' => 'https://appleid.apple.com',
                'sub' => $this->clientId,
            ],
            $this->privateKey
        );
    }

    private function assertClaims(array $claims): void
    {
        $subject = $claims['sub'] ?? null;
        if (!is_string($subject) || $subject === '') {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        if (($claims['iss'] ?? null) !== 'https://appleid.apple.com') {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        $audience = $claims['aud'] ?? null;
        $audienceOk = $audience === $this->clientId
            || (is_array($audience) && in_array($this->clientId, $audience, true));
        if (!$audienceOk) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        if (!isset($claims['exp']) || !is_numeric($claims['exp']) || (int) $claims['exp'] < time() - 60) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }
    }

    private function isVerified($value): bool
    {
        return $value === true || $value === 1 || $value === '1' || $value === 'true';
    }

    private function nameFromCallback(array $callback): ?string
    {
        $raw = $callback['user'] ?? null;
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (!is_array($raw) || !isset($raw['name']) || !is_array($raw['name'])) {
            return null;
        }

        $name = trim(($raw['name']['firstName'] ?? '').' '.($raw['name']['lastName'] ?? ''));

        return $name === '' ? null : $name;
    }
}
