<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Team;

class TeamAgentHistory extends Model
{
    protected $table = 'team_agents_history';

    protected $fillable = [
        'team_agent_id', 
        'team_id',
        'agent_type',
        'started_at'
    ];

    protected $dates = [
        'started_at',
        'created_at',
        'updated_at'
    ];

    protected $guarded = [];

    protected $hidden = [];

    /**
     * Get the team agent this history record belongs to
     */
    public function teamAgent() {
        return $this->belongsTo(TeamAgent::class, 'team_agent_id');
    }

    /**
     * Get the team from this history record
     */
    public function team() {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the team name from this history record
     */
    public function getTeamName() {
        if ($this->team) {
            return $this->team->name;
        }
        return trans('general.none');
    }

    /**
     * Get the club name from this history record
     */
    public function getClubName() {
        if ($this->team && $this->team->club) {
            return $this->team->club->name;
        }
        
        return trans('general.none');
    }

    /**
     * Get formatted date when this history record was created
     */
    public function getFormattedDate() {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Get the translated agent type
     */
    public function getAgentTypeTranslated() {
        return trans('agent_types.' . $this->agent_type);
    }

    /**
     * Get the formatted started at date
     */
    public function getFormattedStartedAt() {
        return $this->started_at ? $this->started_at->format('d/m/Y H:i') : null;
    }
}
