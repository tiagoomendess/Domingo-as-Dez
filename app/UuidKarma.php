<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UuidKarma extends Model
{
    protected $table = 'uuid_karmas';

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'karma',
    ];

    protected $guarded = [
        'created_at',
        'updated_at',
    ];

    protected $hidden = [];

    // relates with user by using pivot table UserUuid
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function userUuid()
    {
        return $this->belongsTo('App\UserUuid', 'uuid', 'uuid');
    }

    public function addKarma(int $karma): void
    {
        $this->karma += $karma;
        $this->save();
    }

    public static function addKarmaByUuid(string $uuid, int $karma): void
    {
        $uuid_karma = self::where('uuid', '=', $uuid)->first();
        if (empty($uuid_karma)) {
            return;
        }

        $uuid_karma->addKarma($karma);
    }

    public static function ensureExists(?string $uuid): void
    {
        if (empty($uuid)) {
            return;
        }

        try {
            $uuid_karma = self::where('uuid', '=', $uuid)->first();
            if (empty($uuid_karma)) {
                $uuid_karma = new UuidKarma();
                $uuid_karma->uuid = $uuid;
                $uuid_karma->karma = 0;
                $uuid_karma->save();
            }
        } catch (\Exception $e) {
            Log::error("Error ensuring UUID karma exists: " . $e->getMessage());
        }
    }
}
