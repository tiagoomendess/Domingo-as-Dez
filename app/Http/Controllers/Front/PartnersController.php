<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Partner;
use App\PartnerClick;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PartnersController extends Controller
{
    public function trackClick(Request $request, Partner $partner)
    {
        try {
            $partnerClick = new PartnerClick();
            $partnerClick->partner_id = $partner->id;
            $partnerClick->page = Str::limit(url()->previous(), 155, '');
            $partnerClick->save();
        } catch (\Exception $e) {
            Log::error("Error saving partner click: " . $e->getMessage());
        }

        $referer = urlencode(config('app.url', 'Domingo às Dez'));

        return redirect()->away("$partner->url?from=$referer");
    }
}
