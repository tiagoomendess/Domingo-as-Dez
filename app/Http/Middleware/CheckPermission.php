<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\LegacyPermission;

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
     * @param String $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission = null)
    {

        if (has_permission($permission))
            return $next($request);
        else
            return abort(403, trans('auth.you_need_permission', ['permission' => $permission]));

    }
}
