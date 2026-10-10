<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class NewSitePromptController extends Controller
{
    const COOKIE = 'new_site_prompt';

    const AB_COOKIE = 'new_site_ab';

    const CHOICES = [
        'yes' => 'new_site_prompt_yes',
        'no' => 'new_site_prompt_no',
        'never' => 'new_site_prompt_never',
    ];

    public static function shouldShow()
    {
        if ((int) config('custom.new_version_ab_test', 0) <= 0) {
            return false;
        }

        if (self::destination() === null) {
            return false;
        }

        if (config('custom.new_site_prompt_logged_in_only') && !Auth::check()) {
            return false;
        }

        // Logged-in users always get the prompt. Guests go through the A/B lottery.
        if (!Auth::check() && !self::inAbTestGroup()) {
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

        if ((int) config('custom.new_version_ab_test', 0) <= 0) {
            abort(403);
        }

        if (config('custom.new_site_prompt_logged_in_only') && !Auth::check()) {
            abort(403);
        }

        if (!Auth::check() && !self::inAbTestGroup()) {
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
     * Decide once per visitor whether they are in the prompt group.
     * Result is stored in a cookie so it is not rolled again.
     */
    private static function inAbTestGroup()
    {
        $percentage = (int) config('custom.new_version_ab_test', 0);

        if ($percentage <= 0) {
            return false;
        }

        if ($percentage > 100) {
            $percentage = 100;
        }

        $existing = request()->cookie(self::AB_COOKIE);
        if ($existing !== null && $existing !== '') {
            return (string) $existing === '1';
        }

        $inGroup = random_int(1, 100) <= $percentage;
        Cookie::queue(cookie()->forever(self::AB_COOKIE, $inGroup ? '1' : '0', '/'));

        return $inGroup;
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
