<?php

namespace App\Http\Controllers\Auth;

use App\Notifications\VerifyEmailNotification;
use App\User;
use App\Http\Controllers\Controller;
use App\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/register/verify';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {

        $request->validate([
            'g-recaptcha-response' => 'required|recaptcha',
        ]);

        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        return $this->registered($request, $user)
            ?: redirect($this->redirectTo . '?email=' . urlencode($request->input('email')));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:155',
            'email' => 'required|string|email|max:155|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    public function redirectPath() {
        return $this->redirectTo;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'email_token' => str_random(16),
        ]);
    }

    /**
     * Handles the after registration process
    */
    protected function registered(Request $request, $user)
    {
        $user->notify(new VerifyEmailNotification($user->email, $user->email_token));

        UserProfile::create([
            'user_id' => $user->id,
        ]);

        Auth::logout();
    }

    public function verifyEmailPage() {

        $email = Input::get('email', 'default');
        $email = urldecode($email);

        if(!$email || $email == 'default')
            return abort(404);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return abort(404);

        return view('auth.verify', ['email' => $email]);
    }

    public function verifyEmail($email, $token) {

        $user = User::where('email', $email)->first();

        //If the user is already verified
        if ($user->verified == true) {

            $errors = new MessageBag();
            $errors->add('already_verified', trans('auth.already_verified'));
            return redirect()->route('verifyEmailPage')->withErrors($errors);

        }

        if ($token == $user->email_token) {

            $user->verified = true;
            $user->email_token = null;
            $user->save();

        } else {

            $errors = new MessageBag();
            $errors->add('verify_token_mismatch', trans('auth.verify_token_mismatch'));
            return redirect()->route('verifyEmailPage')->withErrors($errors);

        }

        return redirect()->route('login');
    }

    public function resendVerifyEmail(Request $request) {

    }

}
