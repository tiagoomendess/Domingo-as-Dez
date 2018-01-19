<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = ['name', 'competition_id', 'relegates', 'promotes', 'visible'];

    protected $guarded = [];

    protected $hidden = [];
}
