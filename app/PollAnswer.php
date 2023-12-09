<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollAnswer extends Model
{
    protected $fillable = [
        'answer',
        'poll_id',
    ];

    public function votes()
    {
        return $this->hasMany(PollAnswerVote::class);
    }

    public function poll() {
        return $this->belongsTo(Poll::class);
    }
}
