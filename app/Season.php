<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = ['competition_id', 'relegates', 'promotes', 'start_year', 'end_year', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function competition() {
        return $this->belongsTo('App\Competition');
    }

    public function games() {
        return $this->hasMany('App\Game');
    }
}
