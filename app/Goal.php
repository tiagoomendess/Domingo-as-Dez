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
                return $this->player->nickname;
            else
                return null;
        } else {
            return null;
        }
    }

    public function getPlayerPicture() {

        if ($this->player)
            return $this->player->getPicture();
        else
            return config('custom.default_profile_pic');

    }

    public function getPlayerId() {

        if ($this->player) {
            return $this->player->id;
        } else {
            return null;
        }
    }
}
