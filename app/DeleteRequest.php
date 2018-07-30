<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DeleteRequest extends Model
{
    protected $fillable = ['user_id', 'verification_code', 'verified', 'cancelled', 'motive', 'processed'];

    protected $guarded = [];

    protected $hidden = [];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
