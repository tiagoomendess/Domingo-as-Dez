<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playground extends Model
{
    protected $fillable = ['club_id', 'name', 'surface', 'width', 'height', 'capacity', 'picture', 'visible'];

    protected $guarded = [];

    protected $hidden = [];
}
