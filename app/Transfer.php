<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = ['player_id', 'team_id', 'date', 'visible'];

    protected $guarded = [];

    protected $hidden = [];
}
