<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = ['name', 'emblem', 'association_page_link', 'website', 'visible'];

    protected $guarded = [];

    protected $hidden = [];
}
