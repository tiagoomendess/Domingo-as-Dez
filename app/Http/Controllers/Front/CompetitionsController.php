<?php

namespace App\Http\Controllers\Front;

use App\Competition;
use App\Game;
use App\Season;
use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

class CompetitionsController extends Controller
{
    public function index() {

    }

    public function show($slug) {


        $comp_name = str_replace('-', ' ', $slug);
        $comp = null;

        $competitions = Competition::all();

        foreach ($competitions as $competition) {

            if(str_slug($competition->name) == $slug  && $competition->visible) {
                $comp = $competition;
                break;
            }

        }

        if(!$comp)
            abort(404);

        return view('front.pages.competition', ['competition' => $competition]);

    }

    public function getRoundInfo($slug, $season, $round) {


        $matches = Game::where('season_id', $season)->where('round', $round)->where('visible', true)->get();

        $season = Season::find($season);

        $i = 0;
        $round_info = array();

        foreach ($matches as $match) {

            $round_info['matches'][$i]['date'] = $match->date;
            $round_info['matches'][$i]['playground_name'] = $match->playground->name;

            $round_info['matches'][$i]['home_club_name'] = $match->homeTeam->club->name;
            $round_info['matches'][$i]['home_club_emblem'] = $match->homeTeam->club->emblem;

            $round_info['matches'][$i]['away_club_name'] = $match->awayTeam->club->name;
            $round_info['matches'][$i]['away_club_emblem'] = $match->awayTeam->club->emblem;

            $i++;

        }

        $games = $season->games->where('round', '<=', $round);

        $unique_teams = $season->getUniqueTeams();

        $sorted_table = $this->sortAFPBLeagueTable($season, $unique_teams);

        dd($sorted_table);

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
    public function sortAFPBLeagueTable($season, $teams) {

        /** RULES
         *  1) Número de pontos alcançados pelos clubes nos jogos disputados entre si
         *  2) Maior diferença entre o número de golos marcados e sofridos nos jogos disputados entre os clubes empatados
         *  3) Maior diferença entre os golos marcados e sofridos, durante toda a competição
         *  4) Maior número de vitórias na competição
         *  5) Maior número de golos marcados na competição
         *  6) Menor número de golos sofridos na competição
         */

        //aux necessary for sorting
        $aux = [
            'team' => null,
            'club_name' => '',
            'points' => 0,
            'gf' => 0,
            'ga' => 0,
            'gd' => 0,
            'v' => 0,
        ];

        $table = array();
        $i = 0;

        //populate table
        foreach ($teams as $team) {

            $table[$i]['team'] = $team;
            $table[$i]['club_name'] = $team->club->name;
            $table[$i]['v'] = $season->getTeamTotalWins($team);
            $table[$i]['points'] = ($table[$i]['v'] * 3) + ($season->getTeamTotalDraws($team));
            $table[$i]['gf'] = $season->getTotalGoalsForTeam($team);
            $table[$i]['ga'] = $season->getTotalGoalsAgainstTeam($team);
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

                                if ($game_between->finished && $game_between->winner->id == $team_i->id)
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



}
