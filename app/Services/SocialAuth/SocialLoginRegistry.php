<?php

namespace App\Services\SocialAuth;

use App\Services\SocialAuth\Drivers\AppleDriver;
use App\Services\SocialAuth\Drivers\FacebookDriver;
use App\Services\SocialAuth\Drivers\GoogleDriver;

class SocialLoginRegistry
{
    const PROVIDERS = ['google', 'facebook', 'apple'];

    public function enabled(): array
    {
        $enabled = [];
        foreach (self::PROVIDERS as $provider) {
            if ($this->isEnabled($provider)) {
                $enabled[] = $provider;
            }
        }

        return $enabled;
    }

    public function isEnabled(string $provider): bool
    {
        if (!in_array($provider, self::PROVIDERS, true)) {
            return false;
        }

        if (!$this->flag('social_logins')) {
            return false;
        }

        return $this->flag($provider.'_login_enabled') && $this->isConfigured($provider);
    }

    public function driver(string $provider): SocialLoginDriver
    {
        if (!$this->isEnabled($provider)) {
            throw new SocialAuthException(SocialAuthException::DISABLED);
        }

        $redirect = $this->redirectUri($provider);
        $http = app(GuzzleOAuthClient::class);

        if ($provider === 'google') {
            return new GoogleDriver(
                $http,
                (string) config('services.google.client_id'),
                (string) config('services.google.client_secret'),
                $redirect
            );
        }

        if ($provider === 'facebook') {
            return new FacebookDriver(
                $http,
                (string) config('services.facebook.client_id'),
                (string) config('services.facebook.client_secret'),
                $redirect,
                (string) config('services.facebook.default_graph_version', 'v24.0')
            );
        }

        return new AppleDriver(
            $http,
            (string) config('services.apple.client_id'),
            $redirect,
            (string) config('services.apple.team_id'),
            (string) config('services.apple.key_id'),
            $this->applePrivateKey()
        );
    }

    public function redirectUri(string $provider): string
    {
        $configured = config('services.'.$provider.'.redirect');
        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        return route('social.callback', ['provider' => $provider]);
    }

    private function isConfigured(string $provider): bool
    {
        $config = config('services.'.$provider);
        if (!is_array($config) || empty($config['client_id'])) {
            return false;
        }

        if ($provider === 'apple') {
            $hasKey = !empty($config['private_key'])
                || (!empty($config['private_key_path']) && is_readable($config['private_key_path']));

            return $hasKey && !empty($config['team_id']) && !empty($config['key_id']);
        }

        return !empty($config['client_secret']);
    }

    private function applePrivateKey(): string
    {
        $path = (string) config('services.apple.private_key_path');
        if ($path !== '' && is_readable($path)) {
            $contents = file_get_contents($path);
            if (is_string($contents) && trim($contents) !== '') {
                return $contents;
            }
        }

        $key = str_replace(["\\r\\n", "\\n"], "\n", (string) config('services.apple.private_key'));
        if (strpos($key, 'BEGIN') === false) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        return $key;
    }

    private function flag(string $key): bool
    {
        return filter_var(config('custom.'.$key), FILTER_VALIDATE_BOOLEAN);
    }
}
