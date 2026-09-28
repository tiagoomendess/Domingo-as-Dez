<?php

namespace App\Services\SocialAuth;

class SocialLoginState
{
    public static function issue(string $provider): string
    {
        return $provider.'.'.bin2hex(random_bytes(20));
    }

    public static function matches(string $provider, string $given, string $sessionState, string $cookieState): bool
    {
        if ($given === '' || strpos($given, $provider.'.') !== 0) {
            return false;
        }

        $sessionOk = $sessionState !== '' && hash_equals($sessionState, $given);
        $cookieOk = $cookieState !== '' && hash_equals($cookieState, $given);

        return $sessionOk || $cookieOk;
    }
}
