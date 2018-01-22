<?php

namespace App\Http\Controllers\Auth;

use App\Notifications\VerifyEmailNotification;
use App\User;
use App\Http\Controllers\Controller;
use App\UserProfile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

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
    protected $redirectTo = '/home';

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
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
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
    }

    public function verifyPage() {
        return 'Verifica o teu email';
    }

    public function verifyEmail($email, $token) {

        $user = User::where('email', $email)->first();

        //If the user is already verified
        if ($user->verified = true) {

            $errors = new MessageBag();
            $errors->add('already_verified', trans('auth.already_verified'));
            return redirect()->route('verifyEmail')->withErrors($errors);

        }

        if ($token == $user->email_token) {

            $user->verified = true;
            $user->save();

        } else {

            $errors = new MessageBag();
            $errors->add('verify_token_mismatch', trans('auth.verify_token_mismatch'));
            return redirect()->route('verifyEmail')->withErrors($errors);

        }

        return redirect()->route('login');
    }

}
