<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefereeType extends Model
{
    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    public function game_referees() {
        return $this->hasMany(GameReferee::class);
    }

}
