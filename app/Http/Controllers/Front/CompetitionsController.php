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

        $comp = null;
        $round_chosen = 0;

        $competitions = Competition::all();

        foreach ($competitions as $competition) {

            if(str_slug($competition->name) == $slug  && $competition->visible) {
                $comp = $competition;
                break;
            }

        }

        if(!$comp)
            abort(404);

        $season = $comp->seasons->first();
        $total_teams = $season->getTotalTeams();

        //Decide wich round to display ---------------------------
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

    public function getRoundInfo($slug, $season, $round) {

        $matches = Game::where('season_id', $season)->where('round', $round)->where('visible', true)->get();

        $season = Season::find($season);

        $i = 0;
        $round_info = array();

        foreach ($matches as $match) {

            $round_info['matches'][$i]['date'] = $match->date;
            $round_info['matches'][$i]['playground_name'] = $match->playground->name;
            $round_info['matches'][$i]['finished'] = $match->finished;

            $round_info['matches'][$i]['home_club_name'] = $match->homeTeam->club->name;
            $round_info['matches'][$i]['home_club_emblem'] = $match->homeTeam->club->emblem;

            $round_info['matches'][$i]['away_club_name'] = $match->awayTeam->club->name;
            $round_info['matches'][$i]['away_club_emblem'] = $match->awayTeam->club->emblem;

            if (!is_null($match->goals_home && !is_null($match->goals_away))) {

                $round_info['matches'][$i]['goals_home'] = $match->goals_home;
                $round_info['matches'][$i]['goals_away'] = $match->goals_away;

            } else {

                $round_info['matches'][$i]['goals_home'] = $match->getTotalHomeGoals();
                $round_info['matches'][$i]['goals_away'] = $match->getTotalAwayGoals();

            }

            if(Carbon::createFromFormat("Y-m-d H:i:s", $match->date)->timestamp < Carbon::now()->timestamp) {
                $round_info['matches'][$i]['started'] = true;
            } else {
                $round_info['matches'][$i]['started'] = false;
            }

            $i++;

        }

        $games = $season->games->where('round', '<=', $round);

        $unique_teams = $season->getUniqueTeams();

        if ($season->competition->competition_type == 'league')
            $sorted_table = $this->sortAFPBLeagueTable($season, $games, $unique_teams);
        else
            $sorted_table = null;

        $round_info['table'] = $sorted_table;

        return response()->json($round_info);

    }


    /**
     *
     * Sorts a league table on behalf of AFPB rules
     * @param $season Season
     * @param $teams Collection
     * @param $games Collection
     *
     * @return array
    */
    public function sortAFPBLeagueTable($season, $games, $teams) {

        /** RULES
         *  1) Número de pontos alcançados pelos clubes nos jogos disputados entre si
         *  2) Maior diferença entre o número de golos marcados e sofridos nos jogos disputados entre os clubes empatados
         *  3) Maior diferença entre os golos marcados e sofridos, durante toda a competição
         *  4) Maior número de vitórias na competição
         *  5) Maior número de golos marcados na competição
         *  6) Menor número de golos sofridos na competição
         */

        $table = array();
        $i = 0;

        //populate table
        foreach ($teams as $team) {

            $table[$i]['team'] = $team;
            $table[$i]['club_name'] = $team->club->name;
            $table[$i]['v'] = $this->getTotalWins($team, $games);
            $table[$i]['d'] = $this->getTotalDraws($team, $games);
            $table[$i]['points'] = ($table[$i]['v'] * 3) + $table[$i]['d'];
            $table[$i]['gf'] = $this->getTotalGoalsForTeam($team, $games);
            $table[$i]['ga'] = $this->getTotalGoalsAgainstTeam($team, $games);
            $table[$i]['gd'] = $table[$i]['gf'] - $table[$i]['ga'];

            $i++;

        }

        for ($i = 0; $i < count($table); $i++) {

            for ($j = $i + 1; $j < count($table); $j++) {

                //Sort by total amount of points
                if($table[$i]['points'] < $table[$j]['points']) {

                    $aux = $table[$i];
                    $table[$i] = $table[$j];
                    $table[$j] = $aux;

                } else if ($table[$i]['points'] == $table[$j]['points']) { //They are tied in points

                    //Rule 1 ------------------------------------------------
                    $team_i = $table[$i]['team'];
                    $team_j = $table[$j]['team'];
                    $games_between = $season->getGamesBetweenTeams($team_i, $team_j);
                    $local_i_points = 0;
                    $local_j_points = 0;

                    foreach ($games_between as $game_between) {

                        if ($game_between->finished) {

                            if ($game_between->isDraw()) {

                                $local_i_points++;
                                $local_j_points++;

                            } else {

                                if ($game_between->finished && $game_between->winner()->id == $team_i->id)
                                    $local_i_points += 3;
                                else
                                    $local_j_points += 3;

                            }

                        }

                    }



                    if ($local_i_points < $local_j_points) {

                        $aux = $table[$i];
                        $table[$i] = $table[$j];
                        $table[$j] = $aux;

                    } else if ($local_i_points == $local_j_points) { //Rule 2

                        $local_gf_i = 0;
                        $local_gf_j = 0;
                        $local_ga_i = 0;
                        $local_ga_j = 0;

                        foreach ($games_between as $game_between) {

                            $goals = $game_between->goals;

                            foreach ($goals as $goal) {

                                if($goal->team->id == $team_i->id) {

                                    $local_gf_i++;
                                    $local_ga_j++;

                                } else if ($goal->team->id == $team_j->id) {

                                    $local_ga_i++;
                                    $local_gf_j++;

                                }
                            }

                        }

                        //Goal difference in the games between them
                        $local_gd_i = $local_gf_i - $local_ga_i;
                        $local_gd_j = $local_gf_j - $local_ga_j;

                        if($local_gd_i < $local_gd_j) {

                            $aux = $table[$i];
                            $table[$i] = $table[$j];
                            $table[$j] = $aux;

                        } else if ($local_gd_i == $local_gd_j) {


                            //Rule 3
                            if($table[$i]['gd'] < $table[$j]['gd']) {

                                $aux = $table[$i];
                                $table[$i] = $table[$j];
                                $table[$j] = $aux;

                            } else if ($table[$i]['gd'] == $table[$j]['gd']) {

                                //Rule 4
                                if($table[$i]['v'] < $table[$j]['v']) {

                                    $aux = $table[$i];
                                    $table[$i] = $table[$j];
                                    $table[$j] = $aux;

                                } else if ($table[$i]['v'] == $table[$j]['v']){

                                    //Rule 5
                                    if($table[$i]['gf'] < $table[$j]['gf']) {

                                        $aux = $table[$i];
                                        $table[$i] = $table[$j];
                                        $table[$j] = $aux;

                                    } else if ($table[$i]['gf'] == $table[$j]['gf']) {

                                        //Rule 6
                                        if($table[$i]['ga'] > $table[$j]['ga']) {

                                            $aux = $table[$i];
                                            $table[$i] = $table[$j];
                                            $table[$j] = $aux;

                                        } else if ($table[$i]['ga'] == $table[$j]['ga']) {

                                            $name_i = $table[$i]['club_name'];
                                            $name_j = $table[$j]['club_name'];

                                            //There are no more rules, alphabetical order now
                                            if ( strcmp($name_i, $name_j) > 0 ) {

                                                $aux = $table[$i];
                                                $table[$i] = $table[$j];
                                                $table[$j] = $aux;

                                            }

                                            //If they are still the same after this, the lowest id in DB will be first

                                        }

                                    }

                                }

                            }
                        }

                    }
                }

            }
        }

        return $table;
    }

    /**
     * Gets the total wins for the team in the games provided
     *
     * @param $team Team
     * @param $games Collection
     *
     * @return int
    */
    public function getTotalWins($team, $games) {

        $total_wins = 0;

        foreach ($games as $game) {

            if($game->finished && !$game->isDraw()) {

                if($game->winner()->id == $team->id)
                    $total_wins++;

            }

        }

        return $total_wins;
    }

    /**
     * Gets the total amount of games that the provided team has drawn in the games provided
     * @param $team Team
     * @param $games Collection
     *
     * @return int
    */
    public function getTotalDraws($team, $games) {

        $total_draws = 0;

        foreach ($games as $game) {

            //Only check games that are draws
            if($game->finished && $game->isDraw()) {

                //Check if the team provided is in that game
                if($game->homeTeam->id == $team->id || $game->awayTeam->id == $team->id)
                    $total_draws++;

            }

        }

        return $total_draws;

    }

    /**
     * Gets the total amount of goals that the provided team has scored in the games provided
     * @param $team Team
     * @param $games Collection
     *
     * @return int
     */
    public function getTotalGoalsForTeam($team, $games) {

        $total_goals = 0;

        foreach ($games as $game) {

            if ($game->homeTeam->id == $team->id)
                $total_goals += $game->getTotalHomeGoals();

            if ($game->awayTeam->id == $team->id)
                $total_goals += $game->getTotalAwayGoals();
        }

        return $total_goals;
    }

    /**
     * Gets the total amount of goals that the provided team has conceded in the games provided
     * @param $team Team
     * @param $games Collection
     *
     * @return int
     */
    public function getTotalGoalsAgainstTeam($team, $games) {

        $total_goals = 0;

        foreach ($games as $game) {

            if ($game->homeTeam->id == $team->id)
                $total_goals += $game->getTotalAwayGoals();

            if ($game->awayTeam->id == $team->id)
                $total_goals += $game->getTotalHomeGoals();
        }

        return $total_goals;
    }

}
