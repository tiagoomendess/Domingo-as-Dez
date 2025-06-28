<?php

namespace App\Http\Controllers\Front;

use App\Club;
use App\Transfer;
use App\Models\TeamAgent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClubController extends Controller
{
    public function index(Request $request) {
        $cacheKey = "all-clubs-page-" . $request->get('page', 1);
        $cached_data = Cache::store('file')->get($cacheKey);
        if (!empty($cached_data)) {
            return view('front.pages.clubs', $cached_data);
        }

        Log::debug("Cache miss for $cacheKey, generating new data");

        $clubs = Club::where('visible', true)
            ->orderByDesc('id')
            ->paginate(10);

        foreach ($clubs as $club) {
            $club->emblem = $club->getEmblem();
            $club->public_url = $club->getPublicUrl();
        }

        $data = [
            'clubs' => $clubs
        ];

        Cache::store('file')->put($cacheKey, $data, 3600);

        return view('front.pages.clubs', $data);
    }

    public function show($club_slug) {

        $club = Club::findByNameSlug($club_slug);

        if (!$club || !$club->visible)
            return abort(404);

        $teams = $club->teams;

        foreach ($teams as $index => $t) {
            if (!$t->visible)
                $teams->forget($index);
        }

        // Load team agents for each team ordered by agent type priority
        foreach ($teams as $team) {
            $team->teamAgents = TeamAgent::where('team_id', $team->id)
                ->with(['player'])
                ->orderByRaw("CASE 
                    WHEN agent_type = 'manager' THEN 1
                    WHEN agent_type = 'assistant_manager' THEN 2
                    WHEN agent_type = 'goalkeeper_manager' THEN 3
                    WHEN agent_type = 'director' THEN 4
                    ELSE 5
                END")
                ->get();
        }

        $transfers = collect();

        foreach ($teams as $team) {

            $transfers_in = $team->transfers;

            $transfers_out = collect();

            foreach ($transfers_in as $t) {
                $nex_transfer = Transfer::where('date', '>', $t->date)->where('player_id', $t->player->id)->orderBy('date', 'asc')->first();

                if (!is_null($nex_transfer))
                    $transfers_out->push($nex_transfer);
            }
        }

        $transfers = $transfers_in->concat($transfers_out);

        $transfers = $transfers->sortByDesc('date');
        $transfers = $transfers->slice(0, 18);

        if (count($club->playgrounds) > 0)
            $playground = $club->getFirstPlayground();
        else
            $playground = null;

        return view(
            'front.pages.club',
            ['club' => $club, 'teams' => $teams, 'playground' => $playground, 'transfers' => $transfers]
        );
    }
}
