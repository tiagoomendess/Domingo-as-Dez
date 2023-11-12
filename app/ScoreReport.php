<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreReport extends Model
{
    protected $fillable = ['user_id', 'game_id', 'home_score', 'away_score', 'source', 'ip_address', 'user_agent'];

    protected $guarded = [];

    protected $hidden = [];

    protected $table = 'score_reports';

    public function game(): BelongsTo
    {
        return $this->belongsTo('App\Game');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }
}
