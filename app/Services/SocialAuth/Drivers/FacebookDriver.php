<?php

namespace App\Services\SocialAuth\Drivers;

use App\Services\SocialAuth\OAuthHttp;
use App\Services\SocialAuth\SocialAuthException;
use App\Services\SocialAuth\SocialIdentity;
use App\Services\SocialAuth\SocialLoginDriver;

class FacebookDriver implements SocialLoginDriver
{
    /** @var OAuthHttp */
    private $http;

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var string */
    private $redirectUrl;

    /** @var string */
    private $graphVersion;

    public function __construct(
        OAuthHttp $http,
        string $clientId,
        string $clientSecret,
        string $redirectUrl,
        string $graphVersion
    ) {
        $this->http = $http;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
        $this->graphVersion = preg_match('/^v\d+\.\d+$/', $graphVersion) ? $graphVersion : 'v24.0';
    }

    public function authorizationUrl(string $state): string
    {
        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'state' => $state,
            'response_type' => 'code',
            'scope' => 'email,public_profile',
        ], '', '&', PHP_QUERY_RFC3986);

        return 'https://www.facebook.com/'.$this->graphVersion.'/dialog/oauth?'.$query;
    }

    public function fetchIdentity(string $code, array $callback = []): SocialIdentity
    {
        $token = $this->http->postForm('https://graph.facebook.com/'.$this->graphVersion.'/oauth/access_token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUrl,
            'code' => $code,
        ]);

        if (empty($token['access_token']) || !is_string($token['access_token'])) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        $accessToken = $token['access_token'];
        $user = $this->http->getJson('https://graph.facebook.com/'.$this->graphVersion.'/me', [
            'fields' => 'id,name,email,picture.type(large)',
            'access_token' => $accessToken,
            'appsecret_proof' => hash_hmac('sha256', $accessToken, $this->clientSecret),
        ]);

        // App-scoped id. The same Facebook app returns the id stored by Socialite.
        if (!isset($user['id']) || (!is_string($user['id']) && !is_int($user['id']))) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        $email = isset($user['email']) && is_string($user['email']) && $user['email'] !== ''
            ? $user['email']
            : null;
        $name = isset($user['name']) && is_string($user['name']) ? $user['name'] : null;
        $avatar = $user['picture']['data']['url'] ?? null;
        if (!is_string($avatar) || !empty($user['picture']['data']['is_silhouette'])) {
            $avatar = null;
        }

        return new SocialIdentity(
            'facebook',
            (string) $user['id'],
            $email,
            $email !== null,
            $name,
            $avatar
        );
    }
}
