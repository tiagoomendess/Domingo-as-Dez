<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $fillable = ['name', 'competition_type', 'picture', 'visible'];

    protected $guarded = [];

    protected $hidden = [];
}
