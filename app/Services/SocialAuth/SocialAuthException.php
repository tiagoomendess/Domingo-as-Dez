<?php

namespace App\Services\SocialAuth;

class SocialAuthException extends \RuntimeException
{
    const MISSING_EMAIL = 'missing_email';
    const UNVERIFIED_EMAIL = 'unverified_email';
    const DISABLED = 'disabled';
    const INVALID_STATE = 'invalid_state';
    const PROVIDER_ERROR = 'provider_error';

    /** @var string */
    private $reason;

    public function __construct(string $reason)
    {
        $this->reason = $reason;
        parent::__construct($reason);
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
