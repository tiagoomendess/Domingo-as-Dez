<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function permissions() {
        return $this->hasMany('App\Permission');
    }

    public function articles() {
        return $this->hasMany('App\Article');
    }

    public function userProfile() {
        return $this->hasOne('App\UserProfile');
    }

    public function userBan() {
        return $this->hasOne('App\UserBan');
    }
}
