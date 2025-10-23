<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class UserUuid extends BaseModel
{
    protected $table = 'user_uuids';

    protected $fillable = [
        'user_id',
        'uuid',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * @param string $uuid
     * @return int|null
     */
    public static function getLastKnownUserId(string $uuid)
    {
        $obj = self::where('uuid', '=', $uuid)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($obj)) {
            return null;
        }

        return (int) $obj->user_id;
    }

    public static function exists(int $user_id, string $uuid): bool
    {
        if (empty($user_id) || empty($uuid)) {
            return false;
        }

        $user_uuid = UserUuid::where('user_id', '=', $user_id)
            ->where('uuid', '=', $uuid)
            ->first();

        if (empty($user_uuid)) {
            return false;
        }

        return true;
    }

    public static function addIfNotExist(int $user_id, string $uuid): bool
    {
        if (empty($user_id) || empty($uuid)) {
            Log::info("Cannot save new UserUuid because either user_id($user_id) or uuid($uuid) is empty");
            return false;
        }

        if (self::exists($user_id, $uuid)) {
            return true;
        }

        $user_uuid = new UserUuid();
        $user_uuid->user_id = $user_id;
        $user_uuid->uuid = Str::limit($uuid, 36, '');

        try {
            $user_uuid->save();
        } catch (\Exception $e) {
            Log::error("Error saving new UserUuid: " . $e->getMessage());
            return false;
        }

        return true;
    }

    public function uuidKarma()
    {
        return $this->hasOne(UuidKarma::class, 'uuid', 'uuid');
    }

    public function getRelated(): Collection
    {
        return self::where('user_id', '=', $this->user_id)
          ->where('uuid', '!=', $this->uuid)
          ->get();
    }
}
