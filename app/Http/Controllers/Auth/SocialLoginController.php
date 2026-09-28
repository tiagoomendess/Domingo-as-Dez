<?php

namespace App\Http\Controllers\Auth;

use App\Audit;
use App\Http\Controllers\Controller;
use App\Services\SocialAuth\SocialAuthException;
use App\Services\SocialAuth\SocialAccountService;
use App\Services\SocialAuth\SocialLoginRegistry;
use App\Services\SocialAuth\SocialLoginState;
use App\UserUuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;

class SocialLoginController extends Controller
{
    const STATE_COOKIE = 'social_login_state';

    /** @var SocialLoginRegistry */
    private $registry;

    /** @var SocialAccountService */
    private $accounts;

    public function __construct(SocialLoginRegistry $registry, SocialAccountService $accounts)
    {
        $this->middleware('guest');
        $this->registry = $registry;
        $this->accounts = $accounts;
    }

    public function redirectToProvider(Request $request, string $provider)
    {
        if (!$this->registry->isEnabled($provider)) {
            return $this->fail(new SocialAuthException(SocialAuthException::DISABLED));
        }

        $state = SocialLoginState::issue($provider);
        $request->session()->put(self::STATE_COOKIE, $state);

        return redirect()
            ->away($this->registry->driver($provider)->authorizationUrl($state))
            ->withCookie($this->stateCookie($state));
    }

    public function handleProviderCallback(Request $request, string $provider)
    {
        $forgetCookie = $this->forgetStateCookie();

        try {
            $given = (string) $request->input('state', '');
            $sessionState = (string) $request->session()->pull(self::STATE_COOKIE, '');
            $cookieState = (string) $request->cookie(self::STATE_COOKIE, '');

            if (!$this->registry->isEnabled($provider)) {
                throw new SocialAuthException(SocialAuthException::DISABLED);
            }

            if ($request->filled('error') || !SocialLoginState::matches($provider, $given, $sessionState, $cookieState)) {
                throw new SocialAuthException(
                    $request->filled('error')
                        ? SocialAuthException::PROVIDER_ERROR
                        : SocialAuthException::INVALID_STATE
                );
            }

            $code = (string) $request->input('code', '');
            if ($code === '') {
                throw new SocialAuthException(SocialAuthException::PROVIDER_ERROR);
            }

            $identity = $this->registry->driver($provider)->fetchIdentity($code, [
                'user' => $request->input('user'),
            ]);
            $user = $this->accounts->resolve($identity);
        } catch (SocialAuthException $e) {
            Log::info('Social login rejected', [
                'provider' => $provider,
                'reason' => $e->reason(),
            ]);

            return $this->fail($e)->withCookie($forgetCookie);
        } catch (\Throwable $e) {
            Log::error('Social login failed', [
                'provider' => $provider,
                'exception' => get_class($e),
            ]);

            return $this->fail(new SocialAuthException(SocialAuthException::PROVIDER_ERROR))
                ->withCookie($forgetCookie);
        }

        if ($user->isBanned()) {
            Log::info('Banned user tried social login', [
                'user_id' => $user->id,
                'provider' => $provider,
            ]);

            $errors = new MessageBag();
            $errors->add('login', trans('auth.banned'));

            return redirect()->route('login')->withErrors($errors)->withCookie($forgetCookie);
        }

        Auth::login($user);
        Audit::add(Audit::ACTION_LOGIN, 'User', null, $user->toArray());
        UserUuid::addIfNotExist($user->id, $request->cookie('uuid', ''));
        Log::info('User logged in with social provider', [
            'user_id' => $user->id,
            'provider' => $provider,
        ]);

        return redirect()->intended(route('homePage'))->withCookie($forgetCookie);
    }

    private function fail(SocialAuthException $e)
    {
        $errors = new MessageBag();
        $errors->add('login', trans($this->translationKey($e)));

        return redirect()->route('login')->withErrors($errors);
    }

    private function translationKey(SocialAuthException $e): string
    {
        switch ($e->reason()) {
            case SocialAuthException::MISSING_EMAIL:
                return 'auth.we_need_email_access';
            case SocialAuthException::UNVERIFIED_EMAIL:
                return 'auth.social_email_unverified';
            case SocialAuthException::DISABLED:
                return 'auth.social_provider_disabled';
            case SocialAuthException::INVALID_STATE:
                return 'auth.social_state_invalid';
            default:
                return 'auth.social_provider_error';
        }
    }

    private function stateCookie(string $state)
    {
        return cookie()->make(self::STATE_COOKIE, $state, 10, '/', null, true, true, false, 'none');
    }

    private function forgetStateCookie()
    {
        return cookie()->make(self::STATE_COOKIE, '', -2628000, '/', null, true, true, false, 'none');
    }
}
