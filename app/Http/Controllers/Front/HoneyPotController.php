<?php

namespace App\Http\Controllers\Front;

use App\HoneyPotCatch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HoneyPotController extends Controller
{
    public function Get(Request $request)
    {
        try {
            $ip_address = $request->getClientIp();
            $user_agent = $request->userAgent();
            $route = $request->route()->uri();
            $query_params = $request->all();
            $headers = $request->headers->all();
            $cookies = $request->cookies->all();
            $ip_country = $request->header('CF-IPCountry', '-');

            HoneyPotCatch::create([
                'ip_address' => Str::limit($ip_address, 39, ''),
                'ip_country' => Str::limit($ip_country, 2, ''),
                'user_agent' => Str::limit($user_agent, 255, ''),
                'route' => Str::limit($route, 155, ''),
                'query_params' => Str::limit(json_encode($query_params), 500, ''),
                'headers' => Str::limit(json_encode($headers), 65535, ''),
                'cookies' => Str::limit(json_encode($cookies), 65535, ''),
                'http_method' => Str::limit($request->method(), 10),
            ]);

            Log::info("Bad guy catched at $route with ip $ip_address");
        } catch (\Exception $e) {
            Log::error("Error trying to save HoneyPotCatch: " . $e->getMessage());
            Log::error("Request data: " . json_encode($request->all()));
        } finally {
            abort(404);
        }
    }
}
