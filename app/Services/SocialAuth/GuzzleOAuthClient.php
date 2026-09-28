<?php

namespace App\Services\SocialAuth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class GuzzleOAuthClient implements OAuthHttp
{
    /** @var Client */
    private $client;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?: new Client([
            'timeout' => 15,
            'connect_timeout' => 5,
            'http_errors' => true,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function getJson(string $url, array $query = [], array $headers = []): array
    {
        try {
            $response = $this->client->get($url, [
                'query' => $query,
                'headers' => $headers,
            ]);
        } catch (GuzzleException $e) {
            $this->logFailure($e);
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        return $this->decode((string) $response->getBody());
    }

    public function postForm(string $url, array $fields, array $headers = []): array
    {
        try {
            $response = $this->client->post($url, [
                'form_params' => $fields,
                'headers' => $headers,
            ]);
        } catch (GuzzleException $e) {
            $this->logFailure($e);
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        return $this->decode((string) $response->getBody());
    }

    private function decode(string $body): array
    {
        $data = json_decode($body, true, 512, JSON_BIGINT_AS_STRING);

        if (!is_array($data)) {
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        if (isset($data['error'])) {
            Log::warning('OAuth provider returned an error response');
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        return $data;
    }

    private function logFailure(GuzzleException $e): void
    {
        $status = 0;
        if ($e instanceof RequestException && $e->hasResponse()) {
            $status = $e->getResponse()->getStatusCode();
        }

        Log::warning('OAuth HTTP request failed', ['status' => $status]);
    }
}
