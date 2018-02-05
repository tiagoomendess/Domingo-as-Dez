<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserBan extends Model
{
    protected $fillable = ['banned_user_id', 'reason', 'banned_by_user_id', 'pardoned'];

    protected $guarded = [];

    protected $hidden = [];

    public function banned() {
        return $this->belongsTo('App\User', 'banned_user_id');
    }

    public function bannedBy() {
        return $this->belongsTo('App\User', 'banned_by_user_id');
    }

}
