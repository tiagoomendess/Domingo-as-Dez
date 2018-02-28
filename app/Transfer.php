<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = ['player_id', 'team_id', 'date', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function getPreviousTransfer() {

        return Transfer::where('player_id', $this->player->id)->where('date', '<', $this->date)->orderBy('date', 'desc')->first();

    }

}
