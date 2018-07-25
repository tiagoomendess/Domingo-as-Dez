<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['user_id', 'content', 'anonymous', 'credits', 'archived', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function comments() {
        return $this->hasMany('App\MessageComment');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
