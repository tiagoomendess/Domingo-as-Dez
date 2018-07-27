<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DeleteRequest extends Model
{
    protected $fillable = ['user_id', 'effective_date', 'verification_code', 'verified', 'canceled', 'motive'];

    protected $guarded = [];

    protected $hidden = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function isEffective() {

        $now = Carbon::now();

        if (!is_null( $this->effective_date)) {
            $effective_date = Carbon::createFromFormat("Y-m-d H:i:s", $this->effective_date);

            if ($effective_date->timestamp > $now->timestamp)
                return true;
            else
                return false;

        } else {
            return false;
        }
    }
}
