<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name'];

    protected $guarded = [];

    protected $hidden = [];

    public function users() {
        return $this->belongsToMany('App/User', 'user_permissions');
    }


}
