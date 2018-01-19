<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['home_team_id', 'away_team_id', 'season_id', 'round', 'date', 'playground_id', 'goals_home', 'goals_away', 'finished', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function season() {
        return $this->belongsTo('App\Season');
    }

    public function goals() {
        return $this->hasMany('App\Goal');
    }

    public function playground() {
        return $this->belongsTo('App\Playground');
    }

    public function homeTeam() {
        return $this->belongsTo('App/Team', 'home_team_id');
    }

    public function awayTeam() {
        return $this->belongsTo('App/Team', 'away_team_id');
    }
}
