<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScoreReportBan extends BaseModel
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'uuid',
        'expires_at',
        'reason',
        'shadow_ban',
        'ip_ban',
    ];

    public static function findMatch(string $uuid, int $user_id = null, string $ip_address = null, string $user_agent = null)
    {
        $now = Carbon::now();
        $query = DB::table('score_report_bans')
            ->where('expires_at', '>', $now)
            ->where(function ($query) use ($uuid, $user_id, $ip_address) {
                $query->where('uuid', '=', $uuid);

                if (!empty($user_id)) {
                    $query->orWhere('user_id', '=', $user_id);
                }

                if (!empty($ip_address)) {
                    $query->orWhere('ip_address', '=', $ip_address);
                }
            });

        $query->orderBy('id', 'desc');

        $bans = $query->get();
        foreach ($bans as $ban) {
            if ($ban->user_id == $user_id || $ban->uuid == $uuid) {
                return $ban;
            }

            if (!empty($ip_address) && $ban->ip_address == $ip_address && $ban->ip_ban) {
                return $ban;
            }

            if (!empty($ip_address) && $ban->ip_address == $ip_address && $ban->user_agent == $user_agent) {
                return $ban;
            }
        }

        return null;
    }
}
