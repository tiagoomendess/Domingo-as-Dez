<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = ['player_id', 'team_id', 'game_id', 'own_goal', 'penalty', 'minute', 'visible'];

    protected $guarded = [];

    protected $hidden = [];
}
