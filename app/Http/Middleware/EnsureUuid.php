<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class EnsureUuid
{
    public function handle(Request $request, Closure $next)
    {
        $uuid = $request->cookie('uuid');
        if (empty($uuid)) {
            $uuid = Str::limit(Str::uuid(), 36, '');
            $cookie = Cookie::make('uuid', $uuid, 525948);
            Cookie::queue($cookie);
        }

        return $next($request);
    }
}
