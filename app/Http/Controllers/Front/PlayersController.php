<?php

namespace App\Http\Controllers\Front;

use App\Player;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PlayersController extends Controller
{
    public function index(Request $request) {
        $cacheKey = "all-players-page-" . $request->get('page', 1);
        $cached_data = Cache::store('file')->get($cacheKey);
        if (!empty($cached_data)) {
            return view('front.pages.players', $cached_data);
        }

        Log::debug("Cache miss for $cacheKey, generating new data");

        $players = Player::where('visible', true)
            ->orderByDesc('id')
            ->paginate(10);

        foreach ($players as $player) {
            $player->public_url = $player->getPublicURL();
            $player->age_safe_picture = $player->getAgeSafePicture();
        }

        $data = [
            'players' => $players
        ];
        Cache::store('file')->put($cacheKey, $data, 240);

        return view('front.pages.players', $data);
    }

    public function show($id, $name_slug) {

        $player = Player::findOrFail($id);

        if ($name_slug != str_slug($player->name))
            return abort(404);

        if (!$player->visible)
            return abort(404);

        $transfers = $player->transfers;

        foreach ($transfers as $index => $transfer) {

            if (!$transfer->visible)
                $transfers->forget($index);

        }

        $transfers = $transfers->sortByDesc('date');

        return view('front.pages.player', ['player' => $player, 'transfers' => $transfers]);


    }
}
