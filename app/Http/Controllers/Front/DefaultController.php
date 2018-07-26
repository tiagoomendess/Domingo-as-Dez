<?php

namespace App\Http\Controllers\Front;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use ZipArchive;

class DefaultController extends Controller
{
    public function showRGPDInfoPage(Request $request) {

        $user_info = $this::getAllUserInfo();

        if ($request->cookies->has('rgpd_analytics_cookies') && $request->cookie('rgpd_analytics_cookies') == 'true')
            $rgpd_analytics_cookies = true;
        else
            $rgpd_analytics_cookies = false;

        if ($request->cookies->has('rgpd_all_data_collect') && $request->cookie('rgpd_all_data_collect') == 'true')
            $rgpd_all_data_collect = true;
        else
            $rgpd_all_data_collect = false;

        return view('front.pages.rgpd_info', ['user_info' => $user_info, 'rgpd_analytics_cookies' => $rgpd_analytics_cookies, 'rgpd_all_data_collect' => $rgpd_all_data_collect]);
    }

    public function showPrivacyPolicyPage() {
        return view('front.pages.privacy_policy');
    }

    public function showTermsPage() {
        return view('front.pages.terms');
    }

    public static function getAllUserInfo() {

        $user_info = new \stdClass();
        $user_info->user = new \stdClass();
        $user_info->other = array();

        if (Auth::check()){
            $user = Auth::user();
            $profile = $user->profile;

            $user_info->user->name = $user->name;
            $user_info->user->email = $user->email;
            $user_info->user->email_token = $user->email_token;
            $user_info->user->remember_token = $user->remember_token;

            $user_info->profile = new \stdClass();
            $user_info->profile->bio = $profile->bio;
            $user_info->profile->phone = $profile->phone;
            $user_info->profile->picture = url($profile->getPicture());

        }else{
            $user_info = null;
        }

        return $user_info;
    }

    public function downloadUserInfo() {

        $user = Auth::user();
        $files = array();
        $public_dir = public_path('/storage/user_profile_downloads/');

        $json_file_name = $public_dir . str_random(3) . time() . ".json";
        $json_file = fopen($json_file_name, "w") or die("Unable to open file!");
        $txt = json_encode(self::getAllUserInfo());
        fwrite($json_file, $txt);
        fclose($json_file);

        $files[] = $json_file_name;

        if (Auth::check()) {
            $files[] = public_path($user->profile->picture);
        }

        $name = str_random(rand(3,5)) . time() . str_random(rand(3,5)) . '-data.zip';
        $archiveFile = $public_dir . $name;
        $archive = new ZipArchive;

        // check if the archive could be created.
        if ($archive->open($archiveFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            // loop trough all the files and add them to the archive.
            foreach ($files as $file) {
                if ($archive->addFile($file, basename($file))) {
                    // do something here if addFile succeeded, otherwise this statement is unnecessary and can be ignored.
                    continue;
                } else {
                    throw new Exception("file `{$file}` could not be added to the zip file: " . $archive->getStatusString());
                }
            }

            // close the archive.
            if ($archive->close()) {
                unlink($json_file_name);
                return response()->download($archiveFile, basename($archiveFile))->deleteFileAfterSend(true);
            } else {
                throw new \Exception("could not close zip file: " . $archive->getStatusString());
            }
        } else {
            throw new \Exception("zip file could not be created: " . $archive->getStatusString());
        }

    }

    public function setRGPDSettings(Request $request) {

        $validator = Validator::make($request->all(), [
            'rgpd_all_data_collect_switch' => 'nullable|string',
            'rgpd_analytics_cookies_switch' => 'nullable|string',
        ]);

        $rgpd_all_data_cookie = null;

        if ($validator->fails()) {
            return response()->json($request->all());
        }

        $data = new \stdClass();

        if (Auth::check()) {

            $user = Auth::user();

            if ($request->has('rgpd_all_data_collect_switch')) {

                if($request->input('rgpd_all_data_collect_switch') == 'true')
                    $rgpd_all_data_collect_switch = true;
                else
                    $rgpd_all_data_collect_switch = false;

                if ($rgpd_all_data_collect_switch) {

                    if (is_null($user->profile->all_data_consent)) {

                        $user->profile->all_data_consent = Carbon::now()->format("Y-m-d H:i:s");
                        Cookie::queue('rgpd_all_data_collect', 'true');
                        //setcookie('rgpd_all_data_collect', 'true', time() + (10 * 365 * 24 * 60 * 60), "/");
                    }

                } else {
                    if (!is_null($user->profile->all_data_consent)) {
                        $user->profile->all_data_consent = null;
                        Cookie::queue('rgpd_all_data_collect', 'false');
                        //setcookie('rgpd_all_data_collect', 'false', time() + (10 * 365 * 24 * 60 * 60), "/");
                    }
                }
            }

            if ($request->has('rgpd_analytics_cookies_switch')) {

                if($request->input('rgpd_analytics_cookies_switch') == 'true')
                    $rgpd_analytics_cookies_switch = true;
                else
                    $rgpd_analytics_cookies_switch = false;

                if ($rgpd_analytics_cookies_switch) {

                    if (is_null($user->profile->analytics_cookies_consent)) {
                        $user->profile->analytics_cookies_consent = Carbon::now()->format("Y-m-d H:i:s");
                        //setcookie('rgpd_analytics_cookies', 'true', time() + (10 * 365 * 24 * 60 * 60), "/");
                        Cookie::queue('rgpd_analytics_cookies', 'true');
                    }

                } else {
                    if (!is_null($user->profile->analytics_cookies_consent)) {
                        $user->profile->analytics_cookies_consent = null;
                        //setcookie('rgpd_analytics_cookies', 'false', time() + (10 * 365 * 24 * 60 * 60), "/");
                        Cookie::queue('rgpd_analytics_cookies', 'false');
                    }
                }
            }

            $user->save();
            $user->profile->save();

        } else {

            if ($request->has('rgpd_analytics_cookies_switch')) {

                if($request->input('rgpd_analytics_cookies_switch') == 'true')
                    $rgpd_analytics_cookies_switch = true;
                else
                    $rgpd_analytics_cookies_switch = false;

                if ($rgpd_analytics_cookies_switch) {
                    Cookie::queue('rgpd_analytics_cookies', 'true');
                    //setcookie('rgpd_analytics_cookies', 'true', time() + (10 * 365 * 24 * 60 * 60), "/");
                } else {
                    Cookie::queue('rgpd_analytics_cookies', 'false');
                    //setcookie('rgpd_analytics_cookies', 'false', time() + (10 * 365 * 24 * 60 * 60), "/");
                }
            }

        }

        $data->success = true;

        return response()->redirectTo(route('rgpd_info'));
    }
}
