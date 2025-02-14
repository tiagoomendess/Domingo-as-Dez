<?php

namespace App;

use App\Notifications\ScoreReportBanNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ScoreReport extends BaseModel
{
    protected $fillable = [
        'user_id',
        'game_id',
        'home_score',
        'away_score',
        'source',
        'ip_address',
        'ip_country',
        'user_agent',
        'location',
        'location_accuracy',
        'uuid',
        'finished',
        'is_fake',
    ];

    protected $guarded = [];

    protected $hidden = [];

    protected $table = 'score_reports';

    protected $geometry = ['location'];

    protected $geometryAsText = true;

    public function game(): BelongsTo
    {
        return $this->belongsTo('App\Game');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    public function getLatitude()
    {
        if (empty($this->location))
            return null;

        $both = str_replace(['POINT(', ')'], '', $this->location);
        $inArray = explode(' ', $both);

        if (count($inArray) != 2)
            return null;

        return $inArray[0];
    }

    public function getLongitude()
    {
        if (empty($this->location))
            return null;

        $both = str_replace(['POINT(', ')'], '', $this->location);
        $inArray = explode(' ', $both);

        if (count($inArray) != 2)
            return null;

        return $inArray[1];
    }

    public function getGoogleMapsLink()
    {
        $latitude = $this->getLatitude();
        $longitude = $this->getLongitude();

        if (empty($latitude) || empty($longitude))
            return null;

        return "https://www.google.com/maps/search/?api=1&query=$latitude,$longitude";
    }

    public static function GetAlreadySent(int $game_id, string $uuid, $user_id) {
        if (empty($user_id))
            $user_id = 0;

        $query = DB::table('score_reports')
            ->whereRaw("game_id = ? and (uuid = ? OR user_id = ?)", [$game_id, $uuid, $user_id]);

        return $query->get();
    }

    public function banUser() {
        $matchName = $this->game->home_team->club->name . " vs " . $this->game->away_team->club->name;

        Log::info("Banning user for manually flagged fake score report $matchName");
        
        // Check if not already banned
        $ban = ScoreReportBan::findMatch($this->uuid, $this->user_id, $this->ip_address, $this->user_agent);
        if (!empty($ban)) {
            Log::info("User already banned ($ban->id). Skipping...");
            return;
        }

        // not yet banned, ban the user
        $reason = "Envio de um resultado falso no jogo $matchName";

        // check if is really fake, flagged manually
        if (!$this->is_fake){
            Log::info("Cannot ban user, score report $this->id of $matchName ($this->home_score-$this->away_score) is not fake");
            return;
        }

        $user_id = $this->user_id;
        if (empty($user_id)) {
            // Try to sneak the user_id from the uuid match in the past
            $user_id = UserUuid::getLastKnownUserId($this->uuid);
        }

        // Create ScoreReportBan
        $banExpiration = Carbon::now()->addDays(15);
        ScoreReportBan::create([
            'user_id' => $user_id,
            'ip_address' => Str::limit($this->ip_address, 40, ''),
            'user_agent' => Str::limit($this->user_agent, 255, ''),
            'uuid' => Str::limit($this->uuid, 36, ''),
            'expires_at' => $banExpiration,
            'reason' => Str::limit($reason, 255, ''),
            'score_report_id' => $this->id,
        ]);

        if (!empty($user_id)) {
            $this->notifyUser($user_id, $reason, $banExpiration);
        }
    }

    private function notifyUser(int $userId, string $reason, string $expiration)
    {
        try {
            $user = User::where('id', $userId)->first();
            Log::info("Notifying user " . $user->email . " via email about the ban...");
            $user->notify(
                new ScoreReportBanNotification(
                    $expiration,
                    $reason
                )
            );
        } catch (\Exception $e) {
            Log::error("Error sending notification to banned user " . $user->email . ": " . $e->getMessage());
        }
    }
}
