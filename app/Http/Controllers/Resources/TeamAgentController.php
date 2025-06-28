<?php

namespace App\Http\Controllers\Resources;

use App\Audit;
use App\Media;
use App\Models\TeamAgent;
use App\Models\TeamAgentHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Facades\Image;

class TeamAgentController extends Controller
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
        $this->middleware('permission:team_agents.edit')->only(['edit', 'update']);
        $this->middleware('permission:team_agents.create')->only(['create', 'store', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        if ($request->query->get('search')) {
            $teamAgents = TeamAgent::search($request->query->all());
            $teamAgents->load(['team', 'player']);
        } else {
            $teamAgents = TeamAgent::with(['team', 'player'])
                ->orderBy('created_at', 'desc')
                ->paginate(config('custom.results_per_page'));
        }

        return view('backoffice.pages.team_agents', [
            'teamAgents' => $teamAgents,
            'searchFields' => TeamAgent::SEARCH_FIELDS,
            'queryParams' => $request->query->all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('backoffice.pages.create_team_agent');
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

        $teamAgent = TeamAgent::create($validatedData);

        if ($request->hasFile('picture') || $request->filled('picture_url')) {
            $this->handleImageUpload($request, $teamAgent);
        }

        // Always create history record for new team agent
        $this->createHistoryRecord($teamAgent, $request->team_id);

        Audit::add(Audit::ACTION_CREATE, 'TeamAgent', null, $teamAgent->toArray());

        return redirect()
            ->route('team_agents.show', $teamAgent)
            ->with('success', trans('success.model_created', ['model_name' => trans('models.team_agent')]));
    }

    /**
     * Display the specified resource.
     *
     * @param TeamAgent $teamAgent
     * @return View
     */
    public function show(TeamAgent $teamAgent): View
    {
        $teamAgent->load(['team', 'player', 'history.team']);

        return view('backoffice.pages.team_agent', compact('teamAgent'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TeamAgent $teamAgent
     * @return View
     */
    public function edit(TeamAgent $teamAgent): View
    {
        return view('backoffice.pages.edit_team_agent', compact('teamAgent'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param TeamAgent $teamAgent
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TeamAgent $teamAgent)
    {
        $validatedData = $this->validateRequest($request);
        $oldData = $teamAgent->toArray();

        $teamAgent->update($validatedData);

        if ($request->hasFile('picture') || $request->filled('picture_url')) {
            $this->handleImageUpload($request, $teamAgent);
        }

        // Only create history record for updates that change either team or agent type
        if ($request->team_id != $oldData['team_id'] || $request->agent_type != $oldData['agent_type']) {
            $this->createHistoryRecord($teamAgent, $request->team_id);
        }

        Audit::add(Audit::ACTION_UPDATE, 'TeamAgent', $oldData, $teamAgent->toArray());

        return redirect()
            ->route('team_agents.show', $teamAgent)
            ->with('success', trans('success.model_edited', ['model_name' => trans('models.team_agent')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TeamAgent $teamAgent
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TeamAgent $teamAgent)
    {
        $oldData = $teamAgent->toArray();

        $teamAgent->delete();

        Audit::add(Audit::ACTION_DELETE, 'TeamAgent', $oldData);

        return redirect()
            ->route('team_agents.index')
            ->with('success', trans('success.model_deleted', ['model_name' => trans('models.team_agent')]));
    }

    /**
     * Validate the request data.
     *
     * @param Request $request
     * @return array
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:155|min:3',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:14|min:6',
            'player_id' => 'nullable|integer|exists:players,id',
            'team_id' => 'nullable|integer|exists:teams,id',
            'birth_date' => 'nullable|date',
            'external_id' => 'nullable|string|max:155',
            'agent_type' => 'required|string|in:manager,assistant_manager,goalkeeper_manager,director',
            'picture' => 'nullable|file|mimes:png,jpg,jpeg|max:2000',
            'picture_url' => 'nullable|url|max:280',
        ]);
    }

    /**
     * Handle image upload for the team agent.
     *
     * @param Request $request
     * @param TeamAgent $teamAgent
     * @return void
     */
    private function handleImageUpload(Request $request, TeamAgent $teamAgent): void
    {
        $image = null;

        if ($request->hasFile('picture')) {
            $image = Image::make($request->file('picture'));
        } elseif ($request->filled('picture_url')) {
            $image = Image::make($request->picture_url);
        }

        if ($image) {
            $url = MediaController::storeSquareImage($image, $teamAgent->name);

            Media::create([
                'user_id' => Auth::id(),
                'url' => $url,
                'media_type' => 'image',
                'tags' => trans('models.team_agent') . ',' . $teamAgent->name,
            ]);

            $teamAgent->update(['picture' => $url]);
        }
    }

    /**
     * Create a history record for team assignment.
     *
     * @param TeamAgent $teamAgent
     * @param int|null $teamId
     * @return void
     */
    private function createHistoryRecord(TeamAgent $teamAgent, ?int $teamId, $startedAt = null): void
    {
        TeamAgentHistory::create([
            'team_agent_id' => $teamAgent->id,
            'team_id' => $teamId,
            'agent_type' => $teamAgent->agent_type,
            'started_at' => $startedAt ?: now(),
        ]);
    }
}
