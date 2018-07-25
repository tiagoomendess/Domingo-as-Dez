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

    public function getClub() {

        if ($this->team)
            return $this->team->club;
        else
            return null;
}

    public function displayTeamAndClub() {

        if ($this->team)
            return $this->team->club->name . ' (' . $this->team->name . ')';
        else
            return trans('general.none');
    }

    public function getClubName() {

        if($this->team)
            return $this->team->club->name;
        else
            return trans('general.none');
    }

    public function getTeamName() {

        if($this->team)
            return $this->team->name;
        else
            return trans('general.none');
    }

    public function getClubEmblem() {

        if ($this->team)
            return $this->team->club->getEmblem();
        else
            return config('custom.default_emblem');

    }

    public function getPreviousTransfer() {

        return Transfer::where('player_id', $this->player->id)->where('date', '<', $this->date)->orderBy('date', 'desc')->first();

    }

}
