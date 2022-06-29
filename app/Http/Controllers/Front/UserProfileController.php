<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Resources\MediaController;
use App\InfoReport;
use App\Notifications\PasswordChangedNotification;
use App\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Intervention\Image\Facades\Image;

class UserProfileController extends Controller
{

    protected $hasher;

    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
        $this->middleware('auth');
    }

    public function edit()
    {
        $user = Auth::user();
        $infos = InfoReport::where('status', '!=', 'deleted')->where('user_id', $user->id)->get();

        return view('front.pages.edit_profile', ['user' => $user, 'infos' => $infos]);
    }

    public function updateProfileInfo(Request $request) {

        $request->validate([
            'phone' => 'nullable|string|max:14|min:6',
            'bio' => 'nullable|string|max:500|min:3',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $user->profile->phone = $request->input('phone');
        $user->profile->bio = $request->input('bio');
        $user->profile->save();

        return redirect(route('front.userprofile.edit'));

    }

    public function updateProfilePicture(Request $request) {

        $request->validate([
            'photo' => 'required|image|mimes:png,jpg,jpeg|max:9000',
        ]);

        $user = Auth::user();

        if ($user->profile->picture) {

            $url = public_path($user->profile->picture);

            try{
                unlink($url);
            } catch (\Exception $exception) {
                $user->profile->picture = null;
                $user->profile->save();
                return abort(500);
            }
        }

        $image = Image::make($request->file('photo'));

        $url = MediaController::storeSquareImage($image, str_random(9), 400, 'jpg', config('custom.user_avatars_path'));

        $user->profile->picture = $url;
        $user->profile->save();

        return redirect(route('front.userprofile.edit'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password_atual' => 'string|max:50|min:6',
            'nova_password' => 'string|max:50|min:6|confirmed'
        ]);

        $messages = new MessageBag();

        /** @var User $user */
        $user = Auth::user();

        //Social users, ex: Facebook, twitter, cannot change password because they don't have one
        if ($user->isSocial()) {
            $messages->add('error', trans('auth.change_password_not_available_for_social_acc'));
            return redirect()->back()->with(['popup_message' => $messages]);
        }

        $oldPassword = (string)$request->input('password_atual');
        $newPassword = (string)$request->input('nova_password');

        if (!$this->hasher->check($oldPassword, $user->getAuthPassword())) {
            $messages->add('error', trans('auth.current_password_wrong'));
            return redirect()->back()->withErrors($messages);
        }

        if ($oldPassword === $newPassword) {
            $messages->add('error', trans('auth.new_password_cant_be_same_as_old'));
            return redirect()->back()->withErrors($messages);
        }

        $user->password = Hash::make($newPassword);
        $user->save();
        $user->notify(new PasswordChangedNotification());

        $messages->add('success', trans('auth.password_changed_success'));
        return redirect()->back()->with(['popup_message' => $messages]);
    }
}
