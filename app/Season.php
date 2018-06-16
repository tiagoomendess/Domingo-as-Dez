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

    public function game_groups() {
        return $this->hasMany(GameGroup::class);
    }

    public function getName() {

        if ($this->start_year != $this->end_year)
            return $this->start_year . '/' . $this->end_year;
        else
            return $this->start_year;
    }

}
