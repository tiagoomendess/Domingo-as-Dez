<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['club_id', 'name', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function homeGames() {
        return $this->hasMany(Game::class, 'home_team_id');
    }

    public function awayGames() {
        return $this->hasMany(Game::class, 'away_team_id');
    }

    public function club() {
        return $this->belongsTo(Club::class);
    }

    public function goals() {
        return $this->hasMany(Goal::class);
    }

    //returns all the players that passed through that club
    public function players() {
        return $this->belongsToMany(Player::class, 'transfers');
    }

    public function getCurrentPlayers() {

        $all_players = $this->players;

        $current_players = collect();

        foreach($all_players as $player) {

            if($player->getLastTransfer()->team->id == $this->id) {

                if(!$current_players->has($player->id )) {
                    $current_players->put($player->id, $player);
                }
            }

        }

        //I really want a index 0 array ----------------
        $i = 0;
        $array = array();
        foreach ($current_players as $a) {
            $array[$i] = $a;
            $i++;
        }
        //---------------------------------------

        return $array;


    }
}
