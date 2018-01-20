<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageComment extends Model
{
    protected $fillable = ['user_id', 'message_id', 'content', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function message() {
        return $this->belongsTo('App\Message');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
