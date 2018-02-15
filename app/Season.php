<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = ['competition_id', 'relegates', 'promotes', 'start_year', 'end_year', 'obs', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function competition() {
        return $this->belongsTo(Competition::class);
    }

    public function games() {
        return $this->hasMany(Game::class);
    }
}
