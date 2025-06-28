<?php

namespace App\Http\Controllers\Resources;

use App\Audit;
use App\Models\TeamAgent;
use App\Models\TeamAgentHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeamAgentHistoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:team_agents')->only(['index', 'show']);
        $this->middleware('permission:team_agents.edit')->only(['create', 'store', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $history = TeamAgentHistory::with(['teamAgent', 'team'])
            ->orderBy('created_at', 'desc')
            ->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.team_agent_history', compact('history'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        $selectedTeamAgentId = $request->get('team_agent_id');
        
        // Ensure team_agent_id is provided
        if (!$selectedTeamAgentId) {
            return redirect()->route('team_agents.index')
                ->with('error', 'Team agent ID is required to create history.');
        }

        return view('backoffice.pages.create_team_agent_history', compact('selectedTeamAgentId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateRequest($request);
        
        // If no started_at is provided, use current timestamp
        if (!isset($validatedData['started_at'])) {
            $validatedData['started_at'] = now();
        }

        $teamAgentHistory = TeamAgentHistory::create($validatedData);

        Audit::add(Audit::ACTION_CREATE, 'TeamAgentHistory', null, $teamAgentHistory->toArray());

        return redirect()
            ->route('team_agents.show', $teamAgentHistory->teamAgent)
            ->with('success', trans('success.model_created', ['model_name' => trans('models.team_agent_history')]));
    }

    /**
     * Display the specified resource.
     *
     * @param TeamAgentHistory $teamAgentHistory
     * @return View
     */
    public function show(TeamAgentHistory $teamAgentHistory): View
    {
        $teamAgentHistory->load(['teamAgent', 'team']);

        return view('backoffice.pages.team_agent_history_show', compact('teamAgentHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TeamAgentHistory $teamAgentHistory
     * @return View
     */
    public function edit(TeamAgentHistory $teamAgentHistory): View
    {
        $teamAgentHistory->load(['teamAgent', 'team']);

        return view('backoffice.pages.edit_team_agent_history', compact('teamAgentHistory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param TeamAgentHistory $teamAgentHistory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TeamAgentHistory $teamAgentHistory)
    {
        $validatedData = $this->validateRequest($request);
        
        $oldData = $teamAgentHistory->toArray();
        
        $teamAgentHistory->update($validatedData);

        Audit::add(Audit::ACTION_UPDATE, 'TeamAgentHistory', $oldData, $teamAgentHistory->toArray());

        return redirect()
            ->route('team_agents.show', $teamAgentHistory->teamAgent)
            ->with('success', trans('success.model_updated', ['model_name' => trans('models.team_agent_history')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TeamAgentHistory $teamAgentHistory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TeamAgentHistory $teamAgentHistory)
    {
        $teamAgent = $teamAgentHistory->teamAgent;
        $oldData = $teamAgentHistory->toArray();

        $teamAgentHistory->delete();

        Audit::add(Audit::ACTION_DELETE, 'TeamAgentHistory', $oldData);

        return redirect()
            ->route('team_agents.show', $teamAgent)
            ->with('success', trans('success.model_deleted', ['model_name' => trans('models.team_agent_history')]));
    }

    /**
     * Validate the request data.
     *
     * @param Request $request
     * @return array
     */
    private function validateRequest(Request $request): array
    {
        $data = $request->validate([
            'team_agent_id' => 'required|integer|exists:team_agents,id',
            'team_id' => 'nullable|integer|exists:teams,id',
            'agent_type' => 'required|string|in:manager,assistant_manager,goalkeeper_manager,director',
            'started_at' => 'nullable|date',
        ]);

        // Convert empty string to null for team_id
        if (isset($data['team_id']) && $data['team_id'] === '') {
            $data['team_id'] = null;
        }

        return $data;
    }
}
