<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use App\Notifications\MyResetPasswordNotification;
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
        'name', 'email', 'password', 'verified', 'email_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     *  The attributes that should be casted to native types.
     *
     *  @var array
     */
    protected $casts = [
        'settings' => 'array'
    ];

    public function permissions() {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function articles() {
        return $this->hasMany('App\Article');
    }

    public function profile() {
        return $this->hasOne(UserProfile::class);
    }

    public function ban() {
        return $this->hasOne('App\UserBan', 'banned_user_id');
    }

    public function bansGiven()
    {
        return $this->hasMany('App\UserBan', 'banned_by_user_id');
    }

    public function socialProviders()
    {
        return $this->hasMany('App\SocialProvider');
    }

    public function hasPermission($permission) {
        $permissions = $this->permissions;

        foreach ($permissions as $perm) {
            if ($perm->name == $permission || $perm->name == 'admin')
                return true;
        }

        return false;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MyResetPasswordNotification($token));
    }
}