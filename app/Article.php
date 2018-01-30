<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    /**
    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];
    **/

    protected $fillable = ['media_id', 'title', 'description', 'text', 'user_id', 'date', 'tags', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function  media() {
        return $this->belongsTo('App\Media');
    }
}
