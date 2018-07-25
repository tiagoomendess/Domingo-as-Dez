<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playground extends Model
{
    protected $fillable = ['club_id', 'name', 'surface', 'width', 'height', 'capacity', 'picture', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function club() {
        return $this->belongsTo('App\Club');
    }

    public function games() {
        return $this->hasMany('App\Game');
    }

    public function getPicture() {

        if ($this->picture)
            return $this->picture;
        else
            return Media::getPlaceholder('16:9', $this->id);
    }
}
