<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupRulesPosition extends Model
{
    protected $fillable = ['group_rules_id', 'positions', 'color', 'label'];

    protected $guarded = [];

    protected $hidden = [];

    public function group_rules() {
        return $this->belongsTo(GroupRules::class);
    }

    /**
     * Get the positions as an array of integers
     * @return array
     */
    public function getPositionsArray() {
        return array_map('intval', explode(',', $this->positions));
    }

    /**
     * Check if a given position is included in this rule
     * @param int $position
     * @return bool
     */
    public function includesPosition($position) {
        return in_array($position, $this->getPositionsArray());
    }
}
