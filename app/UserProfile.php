<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = ['phone', 'bio', 'user_id', 'picture'];

    protected $guarded = [];

    protected $hidden = [];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
