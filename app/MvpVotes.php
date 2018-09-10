<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MvpVotes extends Model
{
    protected $fillable = ['game_id', 'player_id', 'user_id'];

    public function game() {
        return $this->belongsTo(Game::class);
    }

    public function player() {
        return $this->belongsTo(Player::class);
    }

    public function user() {
        return$this->belongsTo(User::class);
    }
}
