<?php

namespace App\Http\Controllers\Front;

use App\Club;
use App\Transfer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClubController extends Controller
{
    public function show($club_slug) {

        $club = Club::findByNameSlug($club_slug);

        if (!$club || !$club->visible)
            return abort(404);

        $teams = $club->teams;

        foreach ($teams as $index => $t) {
            if (!$t->visible)
                $teams->forget($index);
        }

        $transfers = collect();

        foreach ($teams as $team) {

            $transfers_in = $team->transfers;

            $transfers_out = collect();

            foreach ($transfers_in as $t) {
                $nex_transfer = Transfer::where('date', '>', $t->date)->where('player_id', $t->player->id)->orderBy('date', 'asc')->first();

                if (!is_null($nex_transfer))
                    $transfers_out->push($nex_transfer);
            }
        }

        $transfers = $transfers_in->concat($transfers_out);

        $transfers = $transfers->sortByDesc('date');
        $transfers = $transfers->slice(0, 18);

        if (count($club->playgrounds) > 0)
            $playground = $club->getFirstPlayground();
        else
            $playground = null;

        return view('front.pages.club', ['club' => $club, 'teams' => $teams, 'playground' => $playground, 'transfers' => $transfers]);

    }
}
