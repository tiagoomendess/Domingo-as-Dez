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
}
