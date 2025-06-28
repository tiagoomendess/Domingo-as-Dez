<?php

namespace App\Http\Controllers\Front;

use App\Models\TeamAgent;
use App\Models\TeamAgentHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamAgentController extends Controller
{
    public function show($id, $name_slug = null)
    {
        $agent = TeamAgent::with(['team.club', 'player'])->findOrFail($id);
        
        // Get agent history ordered by started_at descending (most recent first)
        $history = TeamAgentHistory::where('team_agent_id', $agent->id)
            ->with(['team.club'])
            ->orderByDesc('started_at')
            ->get();

        return view('front.pages.team_agent', [
            'agent' => $agent,
            'history' => $history
        ]);
    }
}
