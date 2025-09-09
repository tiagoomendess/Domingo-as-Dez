<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupRules extends Model
{
    protected $fillable = ['name', 'promotes', 'relegates', 'type', 'tie_breaker_script'];

    protected $guarded = [];

    protected $hidden = [];

    public function game_groups() {
        return $this->hasMany(GameGroup::class);
    }

    public function positions() {
        return $this->hasMany(GroupRulesPosition::class);
    }
}
