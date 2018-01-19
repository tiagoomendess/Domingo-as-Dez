<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = ['player_id', 'team_id', 'game_id', 'own_goal', 'penalty', 'minute', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function team() {
        return $this->belongsTo('App\Team');
    }

    public function game() {
        return $this->belongsTo('App\game');
    }

    public function player() {
        return $this->belongsTo('App\Player');
    }
}
