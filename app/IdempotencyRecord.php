<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IdempotencyRecord extends Model
{
    protected $table = 'idempotency_records';

    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'key',
    ];

    /**
     * Check if a key already exists in the idempotency records.
     *
     * @param string $key
     * @return bool
     */
    public static function exists(string $key): bool
    {
        return self::where('key', '=', $key)->exists();
    }

    /**
     * Record a key as processed. Returns true if newly recorded, false if already existed.
     *
     * @param string $key
     * @return bool
     */
    public static function record(string $key): bool
    {
        if (self::exists($key)) {
            return false;
        }

        $record = new self();
        $record->key = $key;
        $record->created_at = now();
        $record->save();

        return true;
    }
}
