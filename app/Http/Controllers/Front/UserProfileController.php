<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Resources\MediaController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UserProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        $user = Auth::user();

        return view('front.pages.edit_profile', ['user' => $user]);
    }

    public function updateProfileInfo(Request $request) {

        $request->validate([
            'phone' => 'nullable|string|max:14|min:6',
            'bio' => 'nullable|string|max:500|min:3',
        ]);

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

}
