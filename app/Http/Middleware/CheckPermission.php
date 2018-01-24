<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Permission;

/**
 * Verifies if the user has the permission stated in the parameter
 **/
class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission = null)
    {

        $user = Auth::user();

        $permissions = $user->permissions;

        foreach ($permissions as $perm) {
            if ($perm->name == $permission || $perm->name == 'admin')
                return $next($request);
        }

        return abort(404, trans('auth.permission_denied'));
    }
}
