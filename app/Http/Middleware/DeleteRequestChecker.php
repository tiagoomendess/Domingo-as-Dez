<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class DeleteRequestChecker
{
    /**
     * Checks if the user has a pending delete request. If it has, he can only see the cancel page, if the request was already processed logout the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (Auth::check()) {

            $user = Auth::user();

            $active_delete_requests = $user->delete_requests->where('cancelled', false);

            foreach ($active_delete_requests as $delete_request) {

                if (!$delete_request->processed) {
                    return view('front.pages.cancel_delete_request', ['user' => $user, 'delete_request' => $delete_request]);
                } else {
                    return redirect()->route('logout');
                }

            }

        }

        return $next($request);
    }
}
