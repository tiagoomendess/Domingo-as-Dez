<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

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
}
