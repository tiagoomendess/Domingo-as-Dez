<?php

if (!function_exists('has_permission')) {

    /*
     * @returns boolean
     * */
    function has_permission($permission_name){

        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user)
            return false;

        $permissions = $user->permissions;

        foreach ($permissions as $perm) {

            if ($perm->name == 'admin')
                return true;
            else if (str_contains($permission_name, $perm->name))
                return true;
            else
                    return true;

        }

        return false;

    }
}

