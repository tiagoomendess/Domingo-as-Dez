<?php

namespace App\Http\Controllers\Auth;

use App\Audit;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\MessageBag;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

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
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {

        $request->validate([
            'g-recaptcha-response' => 'required|recaptcha',
        ]);

        $this->validateEmail($request);
        $email = (string) $request->input('email');

        /** @var User $user */
        $user = User::where('email', $email)->first();
        if ($user) {
            if ($user->isSocial()) {
                $messages = new MessageBag();
                $messages->add('error', 'Esta é uma conta social. Faça login através do botão Facebook, Twitter ou Google.');
                return redirect()->back()->withErrors($messages);
            }
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if (!empty($user)) {
            Audit::add(Audit::ACTION_FORGOT_PASSWORD, null, $user->toArray());
        }

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
}
