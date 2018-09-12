<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = ['phone', 'bio', 'user_id', 'picture', 'account_data_consent', 'analytics_cookies_consent', 'all_data_consent', 'timezone'];

    protected $guarded = [];

    protected $hidden = [];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function getPicture() {

        if ($this->picture)
            return $this->picture;
        else
            return config('custom.default_profile_pic');

    }

    public function consents_analytics_cookies() {

        if (is_null($this->analytics_cookies_consent))
            return false;
        else
            return true;
    }

    public function consents_all_data() {

        if (is_null($this->all_data_consent))
            return false;
        else
            return true;
    }

    public function consents_user_data() {

        if (is_null($this->account_data_consent))
            return false;
        else
            return true;
    }
}
