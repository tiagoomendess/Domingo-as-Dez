<?php

namespace App\Http\Controllers\Resources;

use App\DeleteRequest;
use App\Notifications\VerifyDeleteRequestNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class DeleteRequestsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showDeletePage() {

        $user = Auth::user();

        if (count(DeleteRequest::where('user_id', $user->id)->where('cancelled', false)->get()) > 0)
            return redirect(route('front.userprofile.delete.cancel.show'));

        return view('front.pages.delete_request', ['user' => $user]);

    }

    public function storeDeleteRequest(Request $request) {

        $request->validate([
            'motivo' => 'nullable|string|max:200',
            'understand' => 'required|string',

        ]);

        $user = Auth::user();
        $code = str_random(rand(3,5)) . time() . str_random(rand(6,9));

        if (count(DeleteRequest::where('user_id', $user->id)->where('cancelled', false)->get()) > 0)
            return abort(404);

        $motive = $request->input('motive');

        DeleteRequest::create([
            'user_id' => $user->id,
            'motive' => $motive,
            'verification_code' => $code,
        ]);

        $user->notify(new VerifyDeleteRequestNotification($code));

        return response()->redirectTo(route('front.userprofile.delete.verify.show'));

    }

    public function showVerificationPage() {

        $user = Auth::user();

        $delete_request = $user->delete_requests->where('cancelled', false)->where('verified', false)->first();

        if (!$delete_request)
            return redirect(route('front.userprofile.delete.cancel.show'));

        return view('front.pages.verify_delete_request', ['user' => $user, 'delete_request' => $delete_request]);

    }

    public function verifyCode(Request $request) {

        $request->validate([
            'codigo' => 'required|string|min:10|max:155',
        ]);

        $messages = new MessageBag();
        $code = $request->input('codigo');

        $user = Auth::user();

        if (count($user->delete_requests->where('cancelled', false)) != 1)
            abort(404);

        $delete_request = $user->delete_requests->where('cancelled', false)->first();

        if ($delete_request->verification_code == $code ) {

            $delete_request->verified = true;
            $delete_request->save();
            return response()->redirectTo(route('front.userprofile.delete.cancel.show'));

        } else {

            $messages->add('invalid_code', trans('custom_error.invalid_code'));
            return response()->redirectTo(route('front.userprofile.delete.verify.show'))->withErrors($messages);

        }

    }

    public function cancellationPage() {

        $user = Auth::user();

        $delete_request = $user->delete_requests->where('cancelled', false)->first();

        if (!$delete_request)
            abort(404);

        return view('front.pages.cancel_delete_request', ['user' => $user, 'delete_request' => $delete_request]);

    }

    public function cancelDeleteRequest(Request $request) {

        $afected = false;
        $user = Auth::user();
        $delete_requests = $user->delete_requests->where('cancelled', false);

        foreach ($delete_requests as $del) {

            $afected = true;
            $del->cancelled = true;
            $del->save();
        }

        if ($afected) {

            $messages = new MessageBag();
            $messages->add('delete_cancelled', trans('delete_cancelled'));
            return response()->redirectTo(route('front.userprofile.edit'))->with(['popup_messages' => $messages]);

        } else {

            $errors = new MessageBag();
            $errors->add('unexpected_error', trans('custom_error.unexpected_error'));
            return response()->redirectTo(route('front.userprofile.delete.cancel.show'))->withErrors($errors);
        }

    }
}
