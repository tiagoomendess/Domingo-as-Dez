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

    public function transfers() {
        return $this->hasMany(Transfer::class);
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

    public function getPreviousTeam() {

        $transfers = Transfer::where('player_id', $this->id)->orderBy('date', 'desc')->limit(2)->get();

        if($transfers->count() < 2)
            return null;

        if($transfers->last()->team)
            return $transfers->last()->team;
        else
            return null;

    }

    /**
     * Gets the emblem of the club if it has one, or the default icon
     */
    public function getPicture() {
        if($this->picture)
            return $this->picture;
        else
            return config('custom.default_profile_pic');
    }
}
