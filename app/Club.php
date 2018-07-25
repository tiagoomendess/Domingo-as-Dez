<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = ['name', 'emblem', 'website', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function playgrounds() {
        return $this->hasMany('App\Playground');
    }

    public function teams() {
        return $this->hasMany('App\Team');
    }

    /**
     * Gets the emblem of the club if it has one, or the default icon
    */
    public function getEmblem() {
        if($this->emblem)
            return $this->emblem;
        else
            return config('custom.default_emblem');
    }

    public function getFirstPlayground() {

        return $this->playgrounds->first();

    }

    public static function findByNameSlug($slug) {
        $clubs = Club::all();

        foreach ($clubs as $club) {
            if (str_slug($club->name) == $slug)
                return $club;
        }

        return null;
    }

    public function getPublicURL() {
        return route('front.club.show', ['club_slug' => str_slug($this->name)]);
    }
}
