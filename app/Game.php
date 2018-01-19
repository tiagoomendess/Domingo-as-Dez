<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['home_team_id', 'away_team_id', 'season_id', 'round', 'date', 'playground_id', 'goals_home', 'goals_away', 'finished', 'visible'];

    protected $guarded = [];

    protected $hidden = [];
}
