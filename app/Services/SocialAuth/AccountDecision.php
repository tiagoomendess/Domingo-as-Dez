<?php

namespace App\Services\SocialAuth;

/**
 * Decides how a provider identity maps onto an existing account.
 *
 * A previous social_providers row always wins, so people who registered
 * with the old Socialite login keep that account. Otherwise a verified
 * email is attached to the user who already owns it.
 */
class AccountDecision
{
    const LOGIN_LINKED = 'login_linked';
    const LINK_EMAIL = 'link_email';
    const CREATE = 'create';
    const REJECT_MISSING_EMAIL = 'reject_missing_email';
    const REJECT_UNVERIFIED_EMAIL = 'reject_unverified_email';

    public static function decide(
        bool $hasProviderLink,
        bool $emailMatchesUser,
        ?string $email,
        bool $emailVerified
    ): string {
        if ($hasProviderLink) {
            return self::LOGIN_LINKED;
        }

        if ($email === null || $email === '') {
            return self::REJECT_MISSING_EMAIL;
        }

        if (!$emailVerified) {
            return self::REJECT_UNVERIFIED_EMAIL;
        }

        if ($emailMatchesUser) {
            return self::LINK_EMAIL;
        }

        return self::CREATE;
    }
}
