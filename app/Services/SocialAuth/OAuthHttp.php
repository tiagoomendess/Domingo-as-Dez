<?php

namespace App\Services\SocialAuth;

interface OAuthHttp
{
    public function getJson(string $url, array $query = [], array $headers = []): array;

    public function postForm(string $url, array $fields, array $headers = []): array;
}
