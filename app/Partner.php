<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = ['name', 'url', 'picture', 'priority', 'visible'];

    protected $guarded = [];

    protected $hidden = [];
}
