<?php

namespace App\Http\Middleware;

use \Illuminate\Http\Request;
use \Closure;

class AddAllowCorsHeaders
{
    public function handle(Request $request, Closure $next, string $permission = null)
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}