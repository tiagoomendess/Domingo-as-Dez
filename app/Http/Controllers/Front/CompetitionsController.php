<?php

namespace App\Http\Controllers\Front;

use App\Competition;
use App\Game;
use App\Season;
use App\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class CompetitionsController extends Controller
{
    public function index() {

    }

    public function show($slug) {

        $comp = Competition::getCompetitionBySlug($slug);

        if(!$comp || !$comp->visible)
            abort(404);

        $season = $comp->seasons->first();
        $total_teams = $season->getTotalTeams();

        //Decide wich round to display ---------------------------
        //No futuro usar moda em vez da media
        $now = Carbon::now();

        $past_games = DB::table('games')
            ->where('season_id', $season->id)
            ->where('date', '<', $now->format('Y-m-d H:i:s'))
            ->orderBy('date', 'desc')
            ->limit(($total_teams / 2))
            ->get();

        $futu_games = DB::table('games')
            ->where('season_id', $season->id)
            ->where('date', '>', $now->format('Y-m-d H:i:s'))
            ->orderBy('date', 'asc')
            ->limit(($total_teams / 2))
            ->get();

        // End Decide wich round to display-----------------

        $past_games_avg = 0;
        $futu_games_avg = 0;

        if ($past_games->count() == 0) {

            $round_chosen = 1;

        } else if ($futu_games->count() == 0) {

            $round_chosen = $season->getTotalRounds();

        } else {

            foreach ($past_games as $past_game) {
                $past_games_avg += Carbon::createFromFormat('Y-m-d H:i:s', $past_game->date)->timestamp;
            }

            $past_games_avg = $past_games_avg / $past_games->count();

            foreach ($futu_games as $futu_game) {
                $futu_games_avg += Carbon::createFromFormat('Y-m-d H:i:s', $futu_game->date)->timestamp;
            }

            $futu_games_avg = $futu_games_avg / $futu_games->count();

            $current_timestamp = $now->timestamp;

            //what is closest
            if ($current_timestamp - $past_games_avg < $futu_games_avg - $current_timestamp)
                $round_chosen = $past_games->first()->round;
            else
                $round_chosen = $futu_games->first()->round;

        }

        return view('front.pages.competition', ['competition' => $comp, 'season' => $season, 'round_chosen' => $round_chosen]);

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


}
