<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use ZipArchive;

class DefaultController extends Controller
{
    public function showRGPDInfoPage() {

        $user_info = $this::getAllUserInfo();
        return view('front.pages.rgpd_info', ['user_info' => $user_info]);
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
        $archive = new ZipArchive();

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
}
