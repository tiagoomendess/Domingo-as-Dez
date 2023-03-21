<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = [
        'question',
        'slug',
        'show_results_after',
        'publish_after',
        'close_after',
        'update_image',
        'image',
        'visible',
    ];

    public function answers()
    {
        return $this->hasMany(PollAnswer::class);
    }

    public function getImage(): string
    {
        return $this->image ? $this->image : "/images/poll_default.png";
    }
}
