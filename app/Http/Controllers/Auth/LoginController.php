<?php

namespace App\Http\Controllers\Auth;

use App\Audit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Resources\MediaController;
use App\SocialProvider;
use App\User;
use App\UserProfile;
use App\UserUuid;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use League\Flysystem\Exception;
use Socialite;

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
        $this->middleware('ensure-uuid')->only(['showLoginForm', 'logout']);
    }

    public function logout(Request $request)
    {
        setcookie('rgpd_all_data_collect', 'false', time() - 3400, "/");
        $user = Auth::user();

        $user_data = [];
        if (!empty($user)) {
            UserUuid::addIfNotExist($user->id, $request->cookie('uuid'));
            $user_data = $user->toArray();
        }

        Audit::add(Audit::ACTION_LOGOUT, 'User', $user_data, null);
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

        $password = $request->get('password');
        $sneak_peak_password = Str::limit($password, 3, '');
        // add * for the rest of chars the password has
        for ($i = 3; $i < strlen($password); $i++) {
            $sneak_peak_password.= '*';
        }
        $extra_info = $request->get('email') . ' e password ' . $sneak_peak_password;
        $email = $request->get('email');
        $ip_address = $request->getClientIp() ?? 'Unknown';
        $ip_country = $request->header('CF-IPCountry', 'Unknown');

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            Log::info("User with email $email tried to login too many times from ip $ip_address and country $ip_country");

            return $this->sendLockoutResponse($request);
        }

        //check if the user is verified
        $user = User::where('email', $email)->first();
        $user_array = !empty($user) ? $user->toArray() : null;

        if (!$user || !$user->verified) {
            Audit::add(Audit::ACTION_LOGIN_FAILED, 'User', null, $user_array, $extra_info);
            $this->incrementLoginAttempts($request);
            if (!empty($user)) {
                Log::info("User $user->id with email $email, tried to login but is not verified. 
                IP $ip_address and country $ip_country");
            } else {
                Log::info("User with email $email tried to login but account does not exist. 
                IP $ip_address and country $ip_country");
            }

            return $this->sendFailedLoginResponse($request);
        }

        //check if the user is banned
        if ($user->isBanned()) {
            $this->incrementLoginAttempts($request);
            Log::info("User $user->id tried to login but is banned. IP $ip_address and country $ip_country");
            return $this->sendFailedLoginResponse($request);
        }

        if ($this->attemptLogin($request)) {
            Audit::add(Audit::ACTION_LOGIN, 'User', null, $user->toArray());
            UserUuid::addIfNotExist($user->id, $request->cookie('uuid'));
            Log::info("User $user->id logged in with email($user->email) and password from $ip_address in country $ip_country");
            return $this->sendLoginResponse($request);
        }

        Audit::add(Audit::ACTION_LOGIN_FAILED, 'User', null, $user_array, $extra_info);
        Log::info("User $user->id tried and failed to login with email($user->email) and password from $ip_address in country $ip_country");

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
            Audit::add(Audit::ACTION_LOGIN, 'User', null, $user->toArray());
            UserUuid::addIfNotExist($user->id, request()->cookie('uuid'));
            Log::info("User $user->id logged in with $provider");
        }

        return redirect()->intended($this->redirectTo());
    }

    public function redirectTo() {
        return route('homePage');
    }

    public function showLoginForm(Request $request)
    {
        $uuid = $request->cookie('uuid');
        $response = new Response(view('auth.login'));
        $ip_address = $request->getClientIp() ?? 'Unknown';
        $ip_country = $request->header('CF-IPCountry', 'Unknown');
        if (empty($uuid) || Str::length($uuid) > 36) {
            $uuid = Str::limit(Str::uuid(), 36, '');
            $response->withCookie(cookie('uuid', $uuid, 525948));
        }

        Log::debug("Login page loaded for uuid $uuid from ip $ip_address in country $ip_country");

        return $response;
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
