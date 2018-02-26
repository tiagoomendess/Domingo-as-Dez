<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Team;
use Illuminate\Support\Collection;

class Season extends Model
{
    protected $fillable = ['competition_id', 'relegates', 'promotes', 'start_year', 'end_year', 'obs', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function competition() {
        return $this->belongsTo(Competition::class);
    }

    public function games() {
        return $this->hasMany(Game::class);
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
                if(!is_null($game->goals_home) && !is_null($game->goals_away)) {

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
                if(!is_null($game->goals_home) && !is_null($game->goals_away)) {

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
}
