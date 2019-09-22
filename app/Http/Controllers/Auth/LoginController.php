<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resources\MediaController;
use App\UserProfile;
use Illuminate\Support\Facades\Cookie;
use Intervention\Image\Facades\Image;
use Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\MessageBag;
use App\SocialProvider;
use App\User;
use Illuminate\Http\Request;
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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout(Request $request)
    {
        setcookie('rgpd_all_data_collect', 'false', time() - 3400, "/");
        Auth::logout();
        return redirect()->route('home');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Mixed
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        //check if the user is verified
        $user = User::where('email', $request->get('email'))->first();

        if (!$user || !$user->verified) {
            $this->incrementLoginAttempts($request);
            return $this->sendFailedLoginResponse($request);
        }

        //check if the user is banned
        if ($user->isBanned()) {
            $this->incrementLoginAttempts($request);
            return $this->sendFailedLoginResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
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
            $errors->add('login', trans('auth.we_need_email_access'));
            return redirect()->route('login')->withErrors($errors);
        }

        //If there is not a social provider
        if (!$socialProvider) {

            if (count(User::where('email', $socialUser->getEmail())->get()) > 0) {
                $errors = new MessageBag();
                $errors->add('login', trans('auth.email_already_exists'));
                return redirect()->route('login')->withErrors($errors);
            }

            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'verified' => true,
            ]);

            $image = Image::make($socialUser->getAvatar());

            try {
                $url = MediaController::storeSquareImage($image, str_random(9), 400, 'jpg', config('custom.user_avatars_path'));
            } catch(Exception $e) {
                $url = null;
            }

            UserProfile::create([
                'picture' => $url,
                'user_id' => $user->id,
            ]);


            //Create the socialProvider
            $user->socialProviders()->create([
                'provider_id' => $socialUser->getId(),
                'provider' => $provider,
            ]);

        } else { //there is already a social provider
            $user = $socialProvider->user;
        }

        //If the user is banned
        if ($user->isBanned()) {

            $errors = new MessageBag();
            $errors->add('login', trans('auth.banned'));
            return redirect()->route('login')->withErrors($errors);

        } else { //let him in

            Auth::login($user);

        }

        return redirect()->intended($this->redirectTo());

    }

    public function redirectTo() {
        return route('homePage');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return redirect()->intended($this->redirectTo());
    }
}
