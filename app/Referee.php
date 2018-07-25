<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Referee extends Model
{
    protected $fillable = ['name', 'picture', 'association', 'obs'];

    protected $guarded = [];

    protected $hidden = [];

    public function games() {
        return $this->belongsToMany(Game::class, 'game_referees');
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

    public function getPublicURL() {
        return route('front.referee.show', ['id' => $this->id, 'name_slug' => str_slug($this->name)]);
    }

}
