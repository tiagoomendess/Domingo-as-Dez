<?php

namespace App\Services\SocialAuth;

interface SocialLoginDriver
{
    public function authorizationUrl(string $state): string;

    public function fetchIdentity(string $code, array $callback = []): SocialIdentity;
}
