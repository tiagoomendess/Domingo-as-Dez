<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreReport extends BaseModel
{
    protected $fillable = [
        'user_id',
        'game_id',
        'home_score',
        'away_score',
        'source',
        'ip_address',
        'user_agent',
        'location',
        'location_accuracy',
        'uuid',
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
}
