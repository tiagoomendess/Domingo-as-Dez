<?php

if (!function_exists('has_permission')) {

    /*
     * @returns boolean
     * */
    function has_permission($permission_name){

        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user)
            return false;

        if (is_null($permission_name))
            return true;

        $permissions = $user->permissions;

        foreach ($permissions as $perm) {
            if ($perm->name == 'admin')
                return true;
            else if ($permission_name == $perm->name)
                return true;
        }

        return false;
    }
}
