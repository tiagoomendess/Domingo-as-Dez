<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameReferee extends Model
{
    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    public function referee_type() {
        return $this->belongsTo(RefereeType::class);
    }

    public function game() {
        return $this->belongsTo(Game::class);
    }
}
