<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\UserProfile;
use Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\MessageBag;
use App\SocialProvider;
use App\User;
use Illuminate\Support\Facades\Auth;

use League\Flysystem\Exception;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function authenticate()
    {
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            // Authentication passed...
            return redirect()->intended('dashboard');
        }
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @param $provider  string
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Mixed
     */
    public function handleProviderCallback($provider)
    {

        try {

            $socialUser = Socialite::driver($provider)->user();

        } catch(\Exception $e) {

            $errors = new MessageBag();

            // add your error messages:
            $errors->add('login', trans('auth.failed'));

            return redirect()->route('login')->withErrors($errors);
        }

        $socialProvider = SocialProvider::where('provider_id', $socialUser->getId())->first();

        //If the user don't allow us to see the email
        if (!$socialUser->getEmail()) {
            $errors = new MessageBag();
            $errors->add('login', trans('auth.failed'));
            return redirect()->route('login')->withErrors($errors);
        }

        //If there is not a social provider
        if (!$socialProvider) {

            $user = User::where('email', $socialUser->getEmail())->first();

            //if there is no user, create one
            if (!$user) {

                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'verified' => true,
                ]);

                $userProfile = UserProfile::create([
                    'picture' => $socialUser->getAvatar(),
                    'user_id' => $user->id,
                ]);
            }

            //Create the socialProvider
            $user->socialProviders()->create([
                'provider_id' => $socialUser->getId(),
                'provider' => $provider,
            ]);

        } else { //there is already a social provider
            $user = $socialProvider->user;
        }

        //If the user is banned
        if ($user->ban) {
            $errors = new MessageBag();
            $errors->add('login', trans('auth.banned'));
            return redirect()->route('login')->withErrors($errors);
        } else { //let him in
            Auth::login($user);
        }

        return redirect()->route('home');

    }
}
