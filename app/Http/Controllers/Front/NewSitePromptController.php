<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewSitePromptController extends Controller
{
    const COOKIE = 'new_site_prompt';

    const CHOICES = [
        'yes' => 'new_site_prompt_yes',
        'no' => 'new_site_prompt_no',
        'never' => 'new_site_prompt_never',
    ];

    public static function shouldShow()
    {
        if (self::destination() === null) {
            return false;
        }

        if (config('custom.new_site_prompt_logged_in_only') && !Auth::check()) {
            return false;
        }

        return request()->cookie(self::COOKIE) === null;
    }

    /**
     * New-site URL for visitors who already chose Sim, so they can get back.
     */
    public static function returnUrl()
    {
        if (request()->cookie(self::COOKIE) !== 'yes') {
            return null;
        }

        return self::destination();
    }

    public function choose(Request $request)
    {
        $choice = $request->input('choice');

        if (!isset(self::CHOICES[$choice])) {
            abort(422);
        }

        if (config('custom.new_site_prompt_logged_in_only') && !Auth::check()) {
            abort(403);
        }

        $destination = self::destination();
        if ($choice === 'yes' && $destination === null) {
            abort(404);
        }

        Variable::incrementValue(self::CHOICES[$choice]);

        $cookie = $choice === 'no'
            ? cookie(self::COOKIE, $choice, 60 * 24, '/')
            : cookie()->forever(self::COOKIE, $choice, '/');

        if ($choice === 'yes') {
            return redirect()->away($destination)->withCookie($cookie);
        }

        return redirect()->back()->withCookie($cookie);
    }

    /**
     * Absolute http(s) URL of the new site, or null when it is not usable.
     */
    private static function destination()
    {
        $url = trim((string) config('custom.new_site_url'));

        if ($url === '') {
            return null;
        }

        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return $url;
    }
}
