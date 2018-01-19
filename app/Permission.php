<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['permission', 'user_id'];

    protected $guarded = [];

    protected $hidden = [];

    public function user() {
        return $this->belongsTo('App\User');
    }

}
