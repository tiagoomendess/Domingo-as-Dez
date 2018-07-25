<?php

namespace App\Http\Controllers\Front;

use App\Player;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlayersController extends Controller
{
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
