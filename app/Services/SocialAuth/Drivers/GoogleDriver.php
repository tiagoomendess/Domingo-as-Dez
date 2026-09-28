<?php

namespace App\Services\SocialAuth\Drivers;

use App\Services\SocialAuth\OAuthHttp;
use App\Services\SocialAuth\SocialAuthException;
use App\Services\SocialAuth\SocialIdentity;
use App\Services\SocialAuth\SocialLoginDriver;

class GoogleDriver implements SocialLoginDriver
{
    /** @var OAuthHttp */
    private $http;

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var string */
    private $redirectUrl;

    public function __construct(OAuthHttp $http, string $clientId, string $clientSecret, string $redirectUrl)
    {
        $this->http = $http;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
    }

    public function authorizationUrl(string $state): string
    {
        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'include_granted_scopes' => 'true',
            'prompt' => 'select_account',
        ], '', '&', PHP_QUERY_RFC3986);

        return 'https://accounts.google.com/o/oauth2/v2/auth?'.$query;
    }

    public function fetchIdentity(string $code, array $callback = []): SocialIdentity
    {
        $token = $this->http->postForm('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUrl,
            'grant_type' => 'authorization_code',
        ]);

        if (empty($token['access_token']) || !is_string($token['access_token'])) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        $headers = [
            'Authorization' => 'Bearer '.$token['access_token'],
        ];

        // Socialite 3.4 stored the id from userinfo v2. v3 returns the same
        // person as `sub` if v2 is no longer available.
        try {
            $user = $this->http->getJson('https://www.googleapis.com/userinfo/v2/me', [], $headers);
        } catch (SocialAuthException $e) {
            $user = $this->http->getJson('https://www.googleapis.com/oauth2/v3/userinfo', [], $headers);
        }

        $id = $user['id'] ?? $user['sub'] ?? null;
        if (!is_string($id) && !is_int($id)) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        $email = isset($user['email']) && is_string($user['email']) ? $user['email'] : null;
        $name = isset($user['name']) && is_string($user['name']) ? $user['name'] : null;
        $avatar = isset($user['picture']) && is_string($user['picture']) ? $user['picture'] : null;
        $verified = $user['verified_email'] ?? $user['email_verified'] ?? false;

        return new SocialIdentity(
            'google',
            (string) $id,
            $email,
            $verified === true || $verified === 1 || $verified === 'true',
            $name,
            $avatar
        );
    }
}
