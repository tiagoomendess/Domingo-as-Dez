<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckBan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        //Check id there is a user
        if (Auth::check()) {

            //Getting the user
            $user = Auth::user();

            //if the user is banned, don't accept the request
            if ($user->isBanned())
                return abort(403, trans('auth.banned'));

        }

        return $next($request);
    }
}
