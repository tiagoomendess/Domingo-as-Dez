<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = ['name', 'emblem', 'association_page_link', 'website', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function playgrounds() {
        return $this->hasMany('App\Playground');
    }

    public function teams() {
        return $this->hasMany('App\Team');
    }
}
