<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = ['name', 'picture', 'association_id', 'nickname', 'phone', 'email', 'facebook_profile', 'obs', 'position', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function goals() {
        return $this->hasMany('App\Goal');
    }

    public function teams() {
        return $this->belongsToMany('App\Team', 'transfers');
    }

    public function getTeam() {

        $last_transfer = $this->getLastTransfer();

        if ($last_transfer != null)
            return $last_transfer->team;
        else
            return null;
    }

    public function getClub() {

        $team = $this->getTeam();

        if($team)
            return $team->club;
        else
            return null;

    }

    public function getLastTransfer() {

        return Transfer::where('player_id', $this->id)->orderBy('date', 'desc')->first();
    }
}
