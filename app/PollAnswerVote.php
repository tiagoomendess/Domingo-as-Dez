<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollAnswerVote extends Model
{
    protected $fillable = [
        'poll_answer_id',
        'user_id',
        'ip'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function answer() {
        return $this->belongsTo(PollAnswer::class);
    }
}
