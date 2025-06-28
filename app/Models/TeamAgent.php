<?php

namespace App\Models;

use App\SearchableModel;
use Illuminate\Support\Str;
use App\Team;
use App\Player;

class TeamAgent extends SearchableModel
{
    protected $fillable = [
        'player_id', 
        'team_id', 
        'name', 
        'birth_date', 
        'external_id', 
        'email', 
        'phone', 
        'picture',
        'agent_type'
    ];

    protected $guarded = [];

    protected $hidden = [];

    public const SEARCH_FIELDS = [
        'name' => [
            'name' => 'name',
            'type' => 'string',
            'trans' => 'Nome',
            'allowSearch' => true,
            'compare' => 'like',
            'validation' => 'nullable|min:3|max:30|string'
        ],
        'id' => [
            'name' => 'id',
            'type' => 'integer',
            'trans' => 'Id',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|integer'
        ],
        'created_at' => [
            'name' => 'created_at',
            'type' => 'date',
            'trans' => 'Data de Criação',
            'allowSearch' => false
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'type' => 'date',
            'trans' => 'Ultima Atualização',
            'allowSearch' => false
        ]
    ];

    /**
     * Get the team this agent belongs to
     */
    public function team() {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the player if this agent is also a player
     */
    public function player() {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the history records for this agent
     */
    public function history() {
        return $this->hasMany(TeamAgentHistory::class, 'team_agent_id');
    }

    /**
     * Get the agent's picture URL
     */
    public function getPicture() {
        if ($this->picture) {
            return $this->picture;
        }
        return config('custom.default_profile_pic', '/images/default_profile.png');
    }

    /**
     * Get the agent's current team name
     */
    public function getTeamName() {
        if ($this->team) {
            return $this->team->name;
        }
        return trans('general.none');
    }

    /**
     * Get the agent's current club name
     */
    public function getClubName() {
        if ($this->team && $this->team->club) {
            return $this->team->club->name;
        }
        return trans('general.none');
    }

    /**
     * Check if the agent is currently active (has a team)
     */
    public function isActive() {
        return !is_null($this->team_id);
    }

    /**
     * Get formatted birth date
     */
    public function getFormattedBirthDate() {
        if ($this->birth_date) {
            return \Carbon\Carbon::parse($this->birth_date)->format('d/m/Y');
        }
        return null;
    }

    /**
     * Get the translated agent type
     */
    public function getAgentTypeTranslated() {
        return trans('agent_types.' . $this->agent_type);
    }

    /**
     * Get the public URL for this team agent
     */
    public function getPublicURL() {
        return route('front.team_agent.show', ['id' => $this->id, 'name_slug' => Str::slug($this->name)]);
    }
}
