<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['user_id', 'url', 'media_type', 'tags', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function  user() {
        return $this->belongsTo('App\User');
    }

    public function  articles() {
        return $this->hasMany(Article::class);
    }

    public function publicUrl() {
        return str_replace('media', 'app_media', $this->url);
    }
}