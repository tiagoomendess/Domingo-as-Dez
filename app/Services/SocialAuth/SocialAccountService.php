<?php

namespace App\Services\SocialAuth;

use App\Http\Controllers\Resources\MediaController;
use App\SocialProvider;
use App\User;
use App\UserProfile;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

/**
 * Resolves a social identity to a user account.
 *
 * Existing social_providers rows (provider + provider_id from the old
 * Socialite login) are reused. If that link is missing, a verified email
 * is attached to the user who already owns it instead of creating a duplicate.
 */
class SocialAccountService
{
    public function resolve(SocialIdentity $identity): User
    {
        $link = $this->findLink($identity);
        if ($link && !$link->user) {
            $link->delete();
            $link = null;
        }

        $email = $this->normalizeEmail($identity->email);
        $emailUser = $email ? User::whereRaw('LOWER(email) = ?', [$email])->first() : null;

        $decision = AccountDecision::decide(
            $link !== null,
            $emailUser !== null,
            $email,
            $identity->emailVerified
        );

        if ($decision === AccountDecision::LOGIN_LINKED) {
            return $link->user;
        }

        if ($decision === AccountDecision::LINK_EMAIL) {
            return $this->linkExisting($emailUser, $identity);
        }

        if ($decision === AccountDecision::CREATE) {
            return $this->createUser($identity, $email);
        }

        throw new SocialAuthException(
            $decision === AccountDecision::REJECT_MISSING_EMAIL
                ? SocialAuthException::MISSING_EMAIL
                : SocialAuthException::UNVERIFIED_EMAIL
        );
    }

    private function findLink(SocialIdentity $identity): ?SocialProvider
    {
        return SocialProvider::whereRaw('LOWER(provider) = ?', [$identity->provider])
            ->where('provider_id', $identity->id)
            ->first();
    }

    private function linkExisting(User $user, SocialIdentity $identity): User
    {
        $this->attach($user, $identity);

        if (!$user->verified) {
            $user->verified = true;
            $user->save();
        }

        Log::info('Social login linked to existing user', [
            'user_id' => $user->id,
            'provider' => $identity->provider,
        ]);

        return $user;
    }

    private function createUser(SocialIdentity $identity, string $email): User
    {
        $avatar = $this->storeAvatar($identity->avatar);

        try {
            return DB::transaction(function () use ($identity, $email, $avatar) {
                $user = User::create([
                    'name' => $this->displayName($identity, $email),
                    'email' => $email,
                    'verified' => true,
                ]);

                UserProfile::create([
                    'picture' => $avatar,
                    'user_id' => $user->id,
                ]);

                $this->attach($user, $identity);

                Log::info('Social login created user', [
                    'user_id' => $user->id,
                    'provider' => $identity->provider,
                ]);

                return $user;
            });
        } catch (QueryException $e) {
            $existing = User::whereRaw('LOWER(email) = ?', [$email])->first();
            if ($existing) {
                return $this->linkExisting($existing, $identity);
            }

            Log::warning('Social login could not create user', [
                'provider' => $identity->provider,
            ]);
            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }
    }

    private function attach(User $user, SocialIdentity $identity): void
    {
        $existing = $this->findLink($identity);
        if ($existing) {
            if ((int) $existing->user_id === (int) $user->id) {
                return;
            }

            throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
        }

        $user->socialProviders()->create([
            'provider_id' => $identity->id,
            'provider' => $identity->provider,
        ]);
    }

    private function normalizeEmail(?string $email): ?string
    {
        if ($email === null) {
            return null;
        }

        $email = Str::lower(trim($email));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $email;
    }

    private function displayName(SocialIdentity $identity, string $email): string
    {
        $name = trim((string) preg_replace('/\s+/', ' ', trim((string) $identity->name)));
        if ($name === '') {
            $name = explode('@', $email)[0];
        }
        if ($name === '') {
            $name = 'Utilizador';
        }

        return Str::limit($name, 150, '');
    }

    private function storeAvatar(?string $url): ?string
    {
        if (!$url || !preg_match('#^https://#i', $url)) {
            return null;
        }

        try {
            $image = Image::make($url);

            return MediaController::storeSquareImage(
                $image,
                Str::random(9),
                400,
                'jpg',
                config('custom.user_avatars_path')
            );
        } catch (\Throwable $e) {
            Log::warning('Social login avatar download failed');

            return null;
        }
    }
}
