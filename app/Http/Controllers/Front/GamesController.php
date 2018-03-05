<?php

namespace App\Http\Controllers\Front;

use App\Competition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GamesController extends Controller
{
    public function show($home_club, $away_club, $competition_slug, $season_start_year, $season_end_year = null) {

        $competition = Competition::getCompetitionBySlug($competition_slug);

        if (!$competition)
            return abort(404);


        $season = $competition->getSeasonByYears($season_start_year, $season_end_year);

        if (!$season)
            return abort(404);

        $game = $season->getGameByClubNameSlug($home_club, $away_club);


        if (!$game || !$game->visible)
            return abort(404);

        dd($game);

    }
}
