<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Game extends Model
{
    protected $fillable = ['home_team_id', 'away_team_id', 'season_id', 'round', 'date', 'playground_id', 'goals_home', 'goals_away', 'finished', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function game_group() {
        return $this->belongsTo(GameGroup::class);
    }

    public function goals() {
        return $this->hasMany(Goal::class);
    }

    public function playground() {
        return $this->belongsTo(Playground::class);
    }

    public function homeTeam() {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function home_team() {
        return $this->homeTeam();
    }

    public function awayTeam() {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function away_team() {
        return $this->awayTeam();
    }

    public function referees() {
        return $this->belongsToMany(Referee::class, 'game_referees');
    }

    public function game_referees() {
        return $this->hasMany(GameReferee::class);
    }

    /**
     * Get total home goals of this game
    */
    public function getTotalHomeGoals() {

        $goals = $this->goals;
        $total_goals = 0;

        foreach ($goals as $goal) {

            if ($goal->team->id == $this->homeTeam->id)
                $total_goals++;

        }

        return $total_goals;
    }

    /**
     * Get total home goals of this game
     */
    public function getTotalAwayGoals() {

        $goals = $this->goals;
        $total_goals = 0;

        foreach ($goals as $goal) {

            if ($goal->team->id == $this->awayTeam->id)
                $total_goals++;

        }

        return $total_goals;
    }

    /**
     * Gets the winner of this game
     *
     * @return Team
    */
    public function winner() {

        if ($this->finished) {

            if ((!is_null($this->goals_home)) && (!is_null($this->goals_away))) {

                if ($this->goals_home > $this->goals_away)
                    return $this->homeTeam;
                else if ($this->goals_home < $this->goals_away)
                    return $this->awayTeam;
                else
                    return null;

            } else {

                if ($this->getTotalHomeGoals() > $this->getTotalAwayGoals())
                    return $this->homeTeam;
                else if ($this->getTotalHomeGoals() < $this->getTotalAwayGoals())
                    return $this->awayTeam;
                else
                    return null;

            }
        } else
            return null;

    }

    public function isDraw() {

        $isDraw = false;

        if ($this->finished) {

            if ((!is_null($this->goals_home)) && (!is_null($this->goals_away))) {

                if ($this->goals_home == $this->goals_away)
                    $isDraw = true;
                else
                    $isDraw = false;

            } else {

                if ($this->getTotalHomeGoals() == $this->getTotalAwayGoals())
                    $isDraw = true;

                else
                    $isDraw = false;

            }

        } else {

            $isDraw = false;

        }

        return $isDraw;

    }

    public function getPublicUrl() {

        if ($this->season->start_year != $this->season->end_year) {

            return route('front.games.show', [
                'home_club' => str_slug($this->homeTeam->club->name),
                'away_club' => str_slug($this->awayTeam->club->name),
                'competition_slug' => str_slug($this->season->competition->name),
                'season_start_year' => str_slug($this->season->start_year),
                'season_end_year' => str_slug($this->season->end_year),
            ]);

        } else {

            return route('front.games.show', [
                'home_club' => str_slug($this->homeTeam->club->name),
                'away_club' => str_slug($this->awayTeam->club->name),
                'competition_slug' => str_slug($this->season->competition->name),
                'season_start_year' => str_slug($this->season->start_year),
            ]);

        }

    }

    public function started() {

        $now = Carbon::now();
        $date = Carbon::createFromFormat("Y-m-d H:i:s", $this->date);

        if ($now->timestamp > $date->timestamp)
            return true;
        else
            return false;
    }

    //gets the score via total goals or via goals_home/away field
    public function getHomeScore() {

        if ($this->goals_home)
            return $this->goals_home;
        else
            return $this->getTotalHomeGoals();
    }

    //gets the score via total goals or via goals_home/away field
    public function getAwayScore() {

        if ($this->goals_away)
            return $this->goals_away;
        else
            return $this->getTotalAwayGoals();
    }
}
