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

        if (!$season)
            abort(404);

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

    public function showDetailedTable($slug) {

        $comp = Competition::getCompetitionBySlug($slug);

        $season = $comp->seasons->first();

        if (!$season)
            abort(404);

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

        $games = Game::where('season_id', $season->id)->where('round', '<=', $round_chosen)->where('visible', true)->get();

        return view('front.pages.detailed_table', ['competition' => $comp, 'season' => $season, 'round_chosen' => $round_chosen]);


    }

}
