<?php

namespace App\Http\Controllers\Front;

use App\Competition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GameGroup;

class GamesController extends Controller
{

    public function show($competition_slug, $season_slug, $group_slug, $round, $clubs_slug) {

        $competition = Competition::getCompetitionBySlug($competition_slug);

        if (!$competition)
            return abort(404);

        $years = mb_split('-', $season_slug, 2);

        if (count($years) == 2)
            $season = $competition->getSeasonByYears($years[0], $years[1]);
        else if (count($years) == 1)
            $season = $competition->getSeasonByYears($years[0], $years[0]);
        else
            return abort(404);

        if (!$season)
            return abort(404);

        $group = $season->getGroupBySlug($group_slug);

        if (!$group)
            return abort(404);

        $clubs = mb_split('-vs-', $clubs_slug, 2);

        if (count($clubs) != 2)
            return abort(404);

        $game = $group->getGameByClubNameSlug($round, $clubs[0], $clubs[1]);

        if (!$game || !$game->visible)
            return abort(404);

        return view('front.pages.game', ['game' => $game]);

    }

    public function liveMatches() {
        return view('front.pages.live_matches');
    }
}


