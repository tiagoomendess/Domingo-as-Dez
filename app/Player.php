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
}
