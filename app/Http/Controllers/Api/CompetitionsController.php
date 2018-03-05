<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Game;
use App\Season;
use App\Competition;
use Carbon\Carbon;

class CompetitionsController extends Controller
{
    public function getGames($slug, $season, $round) {

        if($round < 0 || $round > 1000)
            abort(404);

        $season = Season::findOrFail($season);

        if (!$season->visible)
            abort(404);

        $competition = Competition::getCompetitionBySlug($slug);

        if (!$competition || !$competition->visible)
            abort(404);

        $games = Game::where('season_id', $season->id)->where('round', $round)->where('visible', true)->get();

        if ($games->count() < 1)
            abort(404);

        $matches = null;

        $i = 0;
        foreach ($games as $game) {

            $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $game->date);

            $matches[$i]['date'] = $carbon->format("d/m/Y \à\s H\Hi");
            $matches[$i]['playground_name'] = $game->playground->name;
            $matches[$i]['finished'] = $game->finished;
            $matches[$i]['url'] = $game->getPublicUrl();

            $matches[$i]['home_club_name'] = $game->homeTeam->club->name;
            $matches[$i]['home_club_emblem'] = $game->homeTeam->club->emblem;

            $matches[$i]['away_club_name'] = $game->awayTeam->club->name;
            $matches[$i]['away_club_emblem'] = $game->awayTeam->club->emblem;

            if ((!is_null($game->goals_home)) && (!is_null($game->goals_away))) {

                $matches[$i]['goals_home'] = $game->goals_home;
                $matches[$i]['goals_away'] = $game->goals_away;

            } else {

                $matches[$i]['goals_home'] = $game->getTotalHomeGoals();
                $matches[$i]['goals_away'] = $game->getTotalAwayGoals();

            }

            if(Carbon::createFromFormat("Y-m-d H:i:s", $game->date)->timestamp < Carbon::now()->timestamp) {
                $matches[$i]['started'] = true;
            } else {
                $matches[$i]['started'] = false;
            }

            $i++;

        }

        return response()->json($matches);

    }

    /**
     * Gets the season table for the provided season and round
     */
    public function getTable($slug, $season, $round) {

        if($round < 0 || $round > 1000)
            abort(404);

        $season = Season::findOrFail($season);

        if (!$season->visible)
            abort(404);

        $competition = Competition::getCompetitionBySlug($slug);

        if (!$competition || !$competition->visible)
            abort(404);

        $games = Game::where('season_id', $season->id)->where('round', '<=', $round)->where('visible', true)->get();

        if ($games->count() < 1)
            abort(404);

        if ($competition->competition_type = 'league') {

            switch ($season->table_rules) {

                case 'afpb_league':
                    $table = Season::sortAFPBLeagueTable($season, $games);
                    break;

                default:
                    $table = Season::sortLeagueTable($season, $games);
                    break;
            }

        }

        return response()->json($table);

    }
}
