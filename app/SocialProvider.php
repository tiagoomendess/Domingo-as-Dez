<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialProvider extends Model
{
    protected $fillable = ['provider_id', 'provider'];

    protected $guarded = [];

    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
