<?php

namespace App\Http\Middleware;

use App\UserProfile;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

class SyncUserCookies
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

        if (Auth::check()) {

            $user = Auth::user();

            if (!$request->cookies->has('rgpd_analytics_cookies')) {

                if ($user->profile->consents_analytics_cookies())
                    Cookie::queue('rgpd_analytics_cookies', 'true');
                else
                    Cookie::queue('rgpd_analytics_cookies', 'false');
            } else {

                if (!$user->profile->consents_analytics_cookies() && $request->cookie('rgpd_analytics_cookies') == "true")
                    $user->profile->analytics_cookies_consent = Carbon::now()->format("Y-m-d H:i:s");
            }

            if (!$request->cookies->has('rgpd_all_data_collect')) {

                if ($user->profile->consents_all_data())
                    Cookie::queue('rgpd_all_data_collect', 'true');
                else
                    Cookie::queue('rgpd_all_data_collect', 'false');

            }

            $user->profile->save();
        }

        return $next($request);
    }
}
