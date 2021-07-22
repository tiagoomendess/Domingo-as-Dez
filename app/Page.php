<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['name', 'title', 'slug', 'picture', 'body', 'visible'];
}
