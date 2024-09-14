<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameComment extends Model
{
    protected $fillable = ['uuid', 'pin', 'game_id', 'team_id', 'content', 'deadline', 'used'];

    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    public function team()
    {
        return $this->belongsTo('App\Team');
    }
}
