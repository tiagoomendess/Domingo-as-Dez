<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['club_id', 'name', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function homeGames() {
        return $this->hasMany('App\Game', 'home_team_id');
    }

    public function awayGames() {
        return $this->hasMany('App\Game', 'away_team_id');
    }

    public function club() {
        return $this->belongsTo('App\Club');
    }

    public function goals() {
        return $this->hasMany('App\Goal');
    }

    public function players() {
        return $this->belongsToMany('App\Player', 'transfers');
    }
}
