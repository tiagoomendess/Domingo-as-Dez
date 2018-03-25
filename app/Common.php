<?php

namespace App;

use Illuminate\Support\Facades\Auth;

class Common
{

    public static function hasPermission($permission_string) {

        if (Auth::check()) {

            $user = Auth::user();

            return $user->hasPermission($permission_string);

        } else {
            return false;
        }

    }
}
