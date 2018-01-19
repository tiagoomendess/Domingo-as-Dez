<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['club_id', 'name', 'visible'];

    protected $guarded = [];

    protected $hidden = [];
}
