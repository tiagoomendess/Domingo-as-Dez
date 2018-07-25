<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameReferee extends Model
{
    protected $fillable = ['referee_id', 'game_id', 'referee_type_id'];

    protected $guarded = [];

    protected $hidden = [];

    public function referee() {
        return $this->belongsTo(Referee::class);
    }

    public function referee_type() {
        return $this->belongsTo(RefereeType::class);
    }

    public function game() {
        return $this->belongsTo(Game::class);
    }
}
