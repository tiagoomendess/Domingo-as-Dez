<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['home_team_id', 'away_team_id', 'season_id', 'round', 'date', 'playground_id', 'goals_home', 'goals_away', 'finished', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function season() {
        return $this->belongsTo(Season::class);
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

    public function awayTeam() {
        return $this->belongsTo(Team::class, 'away_team_id');
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

            if (!is_null($this->goals_home) && !is_null($this->goals_away)) {

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

            if (!is_null($this->goals_home) && !is_null($this->goals_away)) {

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
}
