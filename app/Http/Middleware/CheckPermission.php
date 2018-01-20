<?php

namespace App\Http\Middleware;

use Closure;
use App\Permission;
use Auth;

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
            if ($perm->name == $permission)
                return $next($request);
        }

        return abort(403);
    }
}
