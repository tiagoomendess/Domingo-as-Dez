<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = ['player_id', 'team_id', 'game_id', 'own_goal', 'penalty', 'minute', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function team() {
        return $this->belongsTo(Team::class);
    }

    public function game() {
        return $this->belongsTo(Game::class);
    }

    public function player() {
        return $this->belongsTo(Player::class);
    }

    public function getPlayerName() {

        if ($this->player) {
            return $this->player->name;
        } else {
            return trans('general.unknown');
        }
    }

    public function getPlayerNickName() {

        if ($this->player) {
            if($this->player->nickname)
                return $this->player->name;
            else
                return null;
        } else {
            return null;
        }
    }
}
