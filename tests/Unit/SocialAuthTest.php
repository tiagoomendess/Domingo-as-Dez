<?php

namespace Tests\Unit;

use App\Services\SocialAuth\AccountDecision;
use App\Services\SocialAuth\Drivers\AppleDriver;
use App\Services\SocialAuth\Drivers\FacebookDriver;
use App\Services\SocialAuth\Drivers\GoogleDriver;
use App\Services\SocialAuth\Jwt;
use App\Services\SocialAuth\OAuthHttp;
use App\Services\SocialAuth\SocialAuthException;
use App\Services\SocialAuth\SocialLoginState;
use PHPUnit\Framework\TestCase;

class SocialAuthTest extends TestCase
{
    public function testExistingProviderLinkWinsOverEmail()
    {
        $decision = AccountDecision::decide(true, true, 'person@example.com', true);

        $this->assertSame(AccountDecision::LOGIN_LINKED, $decision);
    }

    public function testVerifiedEmailLinksInsteadOfCreating()
    {
        $decision = AccountDecision::decide(false, true, 'person@example.com', true);

        $this->assertSame(AccountDecision::LINK_EMAIL, $decision);
    }

    public function testNewVerifiedEmailCreatesAccount()
    {
        $decision = AccountDecision::decide(false, false, 'person@example.com', true);

        $this->assertSame(AccountDecision::CREATE, $decision);
    }

    public function testMissingEmailDoesNotCreateAccount()
    {
        $decision = AccountDecision::decide(false, false, null, false);

        $this->assertSame(AccountDecision::REJECT_MISSING_EMAIL, $decision);
    }

    public function testUnverifiedEmailDoesNotLinkOrCreate()
    {
        $this->assertSame(
            AccountDecision::REJECT_UNVERIFIED_EMAIL,
            AccountDecision::decide(false, true, 'person@example.com', false)
        );
        $this->assertSame(
            AccountDecision::REJECT_UNVERIFIED_EMAIL,
            AccountDecision::decide(false, false, 'person@example.com', false)
        );
    }

    public function testStateMatchesSessionOrCookieForTheSameProvider()
    {
        $state = SocialLoginState::issue('google');

        $this->assertTrue(SocialLoginState::matches('google', $state, $state, ''));
        $this->assertTrue(SocialLoginState::matches('google', $state, '', $state));
        $this->assertFalse(SocialLoginState::matches('facebook', $state, $state, $state));
        $this->assertFalse(SocialLoginState::matches('google', $state, 'google.other', 'facebook.other'));
        $this->assertFalse(SocialLoginState::matches('google', '', '', ''));
    }

    public function testEs256SignatureVerifies()
    {
        $keys = $this->ecKeys();
        $jwt = Jwt::signEs256(
            ['kid' => 'KEY123'],
            ['iss' => 'TEAM', 'sub' => 'com.example.app'],
            $keys['private']
        );

        $parts = explode('.', $jwt);
        $this->assertCount(3, $parts);

        $header = json_decode(Jwt::base64UrlDecode($parts[0]), true);
        $payload = Jwt::payload($jwt);
        $this->assertSame('ES256', $header['alg']);
        $this->assertSame('KEY123', $header['kid']);
        $this->assertSame('TEAM', $payload['iss']);
        $this->assertSame('com.example.app', $payload['sub']);

        $raw = Jwt::base64UrlDecode($parts[2]);
        $verified = openssl_verify(
            $parts[0].'.'.$parts[1],
            Jwt::rawToDer($raw),
            $keys['public'],
            OPENSSL_ALGO_SHA256
        );

        $this->assertSame(1, $verified);
    }

    public function testGoogleUsesLegacyUserId()
    {
        $http = new FakeOAuthHttp();
        $http->posts[] = ['access_token' => 'token'];
        $http->gets[] = [
            'id' => 'legacy-google-id',
            'sub' => 'different-sub',
            'email' => 'person@example.com',
            'verified_email' => true,
            'name' => 'Person',
            'picture' => 'https://example.com/a.jpg',
        ];

        $identity = (new GoogleDriver($http, 'client', 'secret', 'https://example.com/callback'))
            ->fetchIdentity('auth-code');

        $this->assertSame('google', $identity->provider);
        $this->assertSame('legacy-google-id', $identity->id);
        $this->assertTrue($identity->emailVerified);
        $this->assertSame('https://oauth2.googleapis.com/token', $http->postsUrl[0]);
        $this->assertSame('https://www.googleapis.com/userinfo/v2/me', $http->getsUrl[0]);
    }

    public function testGoogleFallsBackToOpenIdSubject()
    {
        $http = new FakeOAuthHttp();
        $http->posts[] = ['access_token' => 'token'];
        $http->getException = new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        $http->gets[] = [
            'sub' => 'legacy-google-id',
            'email' => 'person@example.com',
            'email_verified' => true,
            'name' => 'Person',
        ];

        $identity = (new GoogleDriver($http, 'client', 'secret', 'https://example.com/callback'))
            ->fetchIdentity('auth-code');

        $this->assertSame('legacy-google-id', $identity->id);
        $this->assertTrue($identity->emailVerified);
        $this->assertSame('https://www.googleapis.com/oauth2/v3/userinfo', $http->getsUrl[1]);
    }

    public function testFacebookUsesAppScopedIdAndCurrentGraphVersion()
    {
        $http = new FakeOAuthHttp();
        $http->posts[] = ['access_token' => 'fb-token'];
        $http->gets[] = [
            'id' => '99887766',
            'name' => 'Person',
            'email' => 'person@example.com',
            'picture' => ['data' => ['url' => 'https://example.com/a.jpg', 'is_silhouette' => false]],
        ];

        $identity = (new FacebookDriver($http, 'app', 'secret', 'https://example.com/callback', 'not-a-version'))
            ->fetchIdentity('auth-code');

        $this->assertSame('facebook', $identity->provider);
        $this->assertSame('99887766', $identity->id);
        $this->assertTrue($identity->emailVerified);
        $this->assertContains('v24.0', $http->postsUrl[0]);
        $this->assertSame(hash_hmac('sha256', 'fb-token', 'secret'), $http->getsQuery[0]['appsecret_proof']);
    }

    public function testAppleReadsSubjectAndFirstAuthorizationName()
    {
        $keys = $this->ecKeys();
        $http = new FakeOAuthHttp();
        $http->posts[] = [
            'id_token' => $this->unsignedJwt([
                'iss' => 'https://appleid.apple.com',
                'aud' => 'com.example.service',
                'sub' => '001234.apple.user',
                'email' => 'person@privaterelay.appleid.com',
                'email_verified' => 'true',
                'exp' => time() + 3600,
            ]),
        ];

        $driver = new AppleDriver(
            $http,
            'com.example.service',
            'https://example.com/login/apple/callback',
            'TEAMID',
            'KEYID',
            $keys['private']
        );

        $identity = $driver->fetchIdentity('auth-code', [
            'user' => json_encode([
                'name' => ['firstName' => 'Tiago', 'lastName' => 'Mendes'],
                'email' => 'person@privaterelay.appleid.com',
            ]),
        ]);

        $this->assertSame('apple', $identity->provider);
        $this->assertSame('001234.apple.user', $identity->id);
        $this->assertSame('Tiago Mendes', $identity->name);
        $this->assertTrue($identity->emailVerified);
        $this->assertSame('https://appleid.apple.com/auth/token', $http->postsUrl[0]);
        $this->assertArrayHasKey('client_secret', $http->postsFields[0]);
    }

    public function testAppleRejectsTokenForAnotherClient()
    {
        $keys = $this->ecKeys();
        $http = new FakeOAuthHttp();
        $http->posts[] = [
            'id_token' => $this->unsignedJwt([
                'iss' => 'https://appleid.apple.com',
                'aud' => 'other.client',
                'sub' => '001234.apple.user',
                'exp' => time() + 3600,
            ]),
        ];

        $driver = new AppleDriver($http, 'com.example.service', 'https://example.com/cb', 'TEAM', 'KEY', $keys['private']);

        $this->expectException(SocialAuthException::class);
        $driver->fetchIdentity('auth-code');
    }

    private function unsignedJwt(array $claims): string
    {
        return Jwt::base64UrlEncode(json_encode(['alg' => 'ES256']))
            .'.'.Jwt::base64UrlEncode(json_encode($claims))
            .'.sig';
    }

    private function ecKeys(): array
    {
        $key = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'prime256v1',
        ]);

        if ($key === false) {
            $this->markTestSkipped('OpenSSL EC support is unavailable');
        }

        openssl_pkey_export($key, $private);
        $details = openssl_pkey_get_details($key);

        return [
            'private' => $private,
            'public' => $details['key'],
        ];
    }
}

class FakeOAuthHttp implements OAuthHttp
{
    public $posts = [];
    public $gets = [];
    public $postsUrl = [];
    public $postsFields = [];
    public $getsUrl = [];
    public $getsQuery = [];
    public $getException;

    public function getJson(string $url, array $query = [], array $headers = []): array
    {
        $this->getsUrl[] = $url;
        $this->getsQuery[] = $query;

        if ($this->getException && $url === 'https://www.googleapis.com/userinfo/v2/me') {
            throw $this->getException;
        }

        return array_shift($this->gets);
    }

    public function postForm(string $url, array $fields, array $headers = []): array
    {
        $this->postsUrl[] = $url;
        $this->postsFields[] = $fields;

        return array_shift($this->posts);
    }
}
