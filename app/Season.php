<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Team;
use Illuminate\Support\Collection;

class Season extends Model
{
    protected $fillable = ['competition_id', 'relegates', 'promotes', 'start_year', 'end_year', 'table_rules', 'obs', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function competition() {
        return $this->belongsTo(Competition::class);
    }

    public function game_group() {
        return $this->hasMany(GameGroup::class);
    }

}
