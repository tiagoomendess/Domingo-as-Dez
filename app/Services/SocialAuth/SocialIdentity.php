<?php

namespace App\Services\SocialAuth;

class SocialIdentity
{
    /** @var string */
    public $provider;

    /** @var string */
    public $id;

    /** @var string|null */
    public $email;

    /** @var bool */
    public $emailVerified;

    /** @var string|null */
    public $name;

    /** @var string|null */
    public $avatar;

    public function __construct(
        string $provider,
        string $id,
        ?string $email,
        bool $emailVerified,
        ?string $name,
        ?string $avatar
    ) {
        $this->provider = $provider;
        $this->id = $id;
        $this->email = $email;
        $this->emailVerified = $emailVerified;
        $this->name = $name;
        $this->avatar = $avatar;
    }
}
