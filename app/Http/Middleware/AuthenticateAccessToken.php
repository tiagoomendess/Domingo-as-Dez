<?php

namespace App\Http\Middleware;

use App\Audit;
use App\Variable;
use Closure;
use Illuminate\Support\Facades\Cache;

class AuthenticateAccessToken
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
        $access_token = $request->header('Authorization');
        if (!$access_token) {
            return response()->json([
                'success' => false,
                'message' => 'No access token provided, access denied'
            ], 401);
        }

        $access_token = str_replace('Bearer', '', $access_token);
        $access_token = trim($access_token);

        $token = Cache::store('file')->get('api_access_token', null);
        if (!$token) {
            $token = Variable::getValue('api_access_token');

            if (empty($token))
                $token = str_random(32);

            Cache::store('file')->put('api_access_token', $token, 10);
        }

        if ($access_token != $token) {
            Audit::add(Audit::ACTION_LOGIN_FAILED, 'Variable', null, $access_token);
            return response()->json([
                'success' => false,
                'message' => 'Invalid access token, access denied'
            ], 401);
        }

        return $next($request);
    }
}
