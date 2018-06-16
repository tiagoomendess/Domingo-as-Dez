<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameGroup extends Model
{
    protected $fillable = ['name', 'season_id', 'group_rules_id'];

    protected $guarded = [];

    protected $hidden = [];

    public function season() {
        return $this->belongsTo(Season::class);
    }

    public function games() {
        return $this->hasMany(Game::class);
    }

    public function group_rules() {
        return $this->belongsTo(GroupRules::class);
    }

    public function getUniqueTeams() {

        $games = $this->games;
        $unique_ids = collect();

        foreach ($games as $game) {

            if (!$unique_ids->contains($game->homeTeam->id))
                $unique_ids->put($game->homeTeam->id, $game->homeTeam->id);

            if (!$unique_ids->contains($game->awayTeam->id))
                $unique_ids->put($game->awayTeam->id, $game->awayTeam->id);

        }

        $unique_teams = collect();
        $i = 0;

        foreach ($unique_ids as $id) {

            $unique_teams->put($i, \App\Team::find($id));
            $i++;
        }

        return $unique_teams;
    }

    public function getTotalTeams() {

        return $this->getUniqueTeams()->count();

    }

    public function getGameByClubNameSlug($home_club_slug, $away_club_slug) {

        $games = $this->games;
        $selected = null;

        foreach ($games as $game) {

            if (str_slug($game->homeTeam->club->name) == $home_club_slug && str_slug($game->awayTeam->club->name) == $away_club_slug)
                $selected = $game;
        }

        return $selected;
    }

    /**
     *
     * Gets te total amount of wins for the provided team
     *
     * @param $team \App\Team
     * @return int
     */
    public function getTeamTotalWins($team) {

        $games = $this->getAllGamesForTeam($team);

        $total_wins = 0;

        foreach ($games as $game) {

            //only take into account games that are finished
            if($game->finished) {

                //if there is a result hard set
                if((!is_null($game->goals_home)) && (!is_null($game->goals_away))) {

                    if($game->id == 20)
                        dd($game);

                    if($game->homeTeam->id == $team->id && $game->goals_home > $game->goals_away)
                        $total_wins ++;
                    else if ($game->awayTeam->id == $team->id && $game->goals_away > $game->goals_home)
                        $total_wins++;

                } else { // Else count the recorded goals

                    $game_goals = $game->goals;
                    $goals_home = 0;
                    $goals_away = 0;

                    foreach ($game_goals as $goal) {

                        if($goal->team->id == $game->homeTeam->id)
                            $goals_home++;
                        else
                            $goals_away++;

                    }

                    if($game->homeTeam->id == $team->id && $goals_home > $goals_away)
                        $total_wins ++;
                    else if ($game->awayTeam->id == $team->id && $goals_away > $goals_home)
                        $total_wins++;

                }
            }
        }

        return $total_wins;
    }

    /**
     *
     * Gets te total amount of draws for the provided team
     *
     * @param $team \App\Team
     * @return int
     */
    public function getTeamTotalDraws($team) {

        $games = $this->getAllGamesForTeam($team);

        $total_draws = 0;

        foreach ($games as $game) {

            //only take into account games that are finished
            if($game->finished) {

                //if there is a result hard set
                if((!is_null($game->goals_home)) && (!is_null($game->goals_away))) {

                    if($game->goals_home == $game->goals_away)
                        $total_draws ++;

                } else { // Else count the recorded goals

                    $game_goals = $game->goals;
                    $goals_home = 0;
                    $goals_away = 0;

                    foreach ($game_goals as $goal) {

                        if($goal->team->id == $game->homeTeam->id)
                            $goals_home++;
                        else
                            $goals_away++;

                    }

                    if($goals_home == $goals_away)
                        $total_draws ++;

                }
            }
        }

        return $total_draws;
    }

    /**
     * Returns the total amount of goals in favor of that team
     */
    public function getTotalGoalsForTeam($team) {

        $games = $this->getAllGamesForTeam($team);
        $total_goals = 0;

        foreach ($games as $game) {

            if($game->finished) {

                $goals = $game->goals;

                foreach ($goals as $goal) {

                    if($goal->team->id == $team->id)
                        $total_goals++;

                }

            }

        }

        return $total_goals;
    }

    /**
     * Returns the total amount of goals against that team
     */
    public function getTotalGoalsAgainstTeam($team) {

        $games = $this->getAllGamesForTeam($team);
        $total_goals = 0;

        foreach ($games as $game) {

            if($game->finished) {

                $goals = $game->goals;

                foreach ($goals as $goal) {
                    if($goal->team->id != $team->id)
                        $total_goals++;
                }

            }

        }

        return $total_goals;
    }

    /**
     * Gets all the games the provided tem is in
     * @param $team \App\Team
     * @return Collection
     */
    public function getAllGamesForTeam($team) {

        $home_games = $this->games->where('home_team_id', $team->id);
        $away_games = $this->games->where('away_team_id', $team->id);

        return $home_games->concat($away_games);

    }


    /**
     * Gets all the games played between the two teams provided in this season
     *
     */
    public function getGamesBetweenTeams($team1, $team2) {

        $games1 = $this->getAllGamesForTeam($team1);
        $games2 = $this->getAllGamesForTeam($team2);


        $games = $games1->intersect($games2);
        $gamesBetween = collect();
        $i = 0;

        foreach ($games as $game) {

            if($game->home_team_id == $team1->id && $game->away_team_id == $team2->id || $game->home_team_id == $team2->id && $game->away_team_id == $team1->id)
                $gamesBetween->put($i, $game);

            $i++;

        }

        return $gamesBetween;
    }


    /**
     * Gets the total amount of rounds that season has
     *
     * @return int
     */
    public function getTotalRounds() {

        $highest_round = 0;

        foreach ($this->games as $game) {

            if ($highest_round < $game->round)
                $highest_round = $game->round;

        }

        return $highest_round;

    }

    /**---------------------------------------------------------------------*/

    /**
     * Sorts a league table only by the points
     * @param $season Season
     * @param $teams Collection
     * @param $games Collection
     *
     * @return array
     */
    public static function sortLeagueTable($season, $games) {
        return array();
    }

    /**
     *
     * Sorts a league table on behalf of AFPB rules
     * @param $season Season
     * @param $games Collection
     *
     * @return array
     */
    public static function sortAFPBLeagueTable($season, $games) {

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

        $teams = $season->getUniqueTeams();

        //populate table
        foreach ($teams as $team) {

            $table[$i]['team'] = $team;
            $table[$i]['club_name'] = $team->club->name;
            $table[$i]['club_emblem'] = $team->club->getEmblem();
            $table[$i]['played'] = self::getPlayed($team, $games);
            $table[$i]['wins'] = self::getWins($team, $games);
            $table[$i]['draws'] = self::getDraws($team, $games);
            $table[$i]['loses'] = self::getLoses($team, $games);
            $table[$i]['points'] = ($table[$i]['wins'] * 3) + $table[$i]['draws'];
            $table[$i]['gf'] = self::getGoalsForTeam($team, $games);
            $table[$i]['ga'] = self::getGoalsAgainstTeam($team, $games);
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
                                if($table[$i]['wins'] < $table[$j]['wins']) {

                                    $aux = $table[$i];
                                    $table[$i] = $table[$j];
                                    $table[$j] = $aux;

                                } else if ($table[$i]['wins'] == $table[$j]['wins']){

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

    public static function getPlayed($team, $games) {

        $played = 0;

        foreach ($games as $game) {
            if($game->homeTeam->id == $team->id || $game->awayTeam->id == $team->id)
                $played++;
        }

        return $played;
    }

    /**
     * Gets the total wins for the team in the games provided
     *
     * @param $team Team
     * @param $games Collection
     *
     * @return int
     */
    public static function getWins($team, $games) {

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
     * Gets the total loses for the team in the games provided
     *
     * @param $team Team
     * @param $games Collection
     *
     * @return int
     */
    public static function getLoses($team, $games) {

        $total_loses = 0;

        foreach ($games as $game) {

            if($game->homeTeam->id == $team->id || $game->awayTeam->id == $team->id) {

                if($game->finished && !$game->isDraw()) {

                    if($game->winner()->id != $team->id)
                        $total_loses++;

                }
            }

        }

        return $total_loses;
    }

    /**
     * Gets the total amount of games that the provided team has drawn in the games provided
     * @param $team Team
     * @param $games Collection
     *
     * @return int
     */
    public static function getDraws($team, $games) {

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
    public static function getGoalsForTeam($team, $games) {

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
    public static function getGoalsAgainstTeam($team, $games) {

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
