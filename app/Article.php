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

    protected $fillable = ['media_type', 'media_link', 'title', 'description', 'text', 'user_id', 'date', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
