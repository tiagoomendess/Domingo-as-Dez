<?php

namespace App;

use Carbon\Carbon;
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
        return $this->belongsTo(Media::class);
    }

    /**
     * Gets the public url for this article
     *
     * @return string
    */
    public function getPublicUrl() {

        $slug = str_slug($this->title);
        $carbon = Carbon::createFromFormat("Y-m-d H:i:s", $this->date);

        return route('news.show', [
            'year' => $carbon->format("Y"),
            'month' => $carbon->format("m"),
            'day' => $carbon->format("d"),
            'slug' => $slug,
        ]);
    }
}
