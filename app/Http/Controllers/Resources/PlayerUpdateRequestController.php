<?php

namespace App\Http\Controllers\Resources;

use App\Audit;
use App\PlayerUpdateRequest;
use App\Player;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PlayerUpdateRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:player_update_requests')->only(['index', 'show']);
        $this->middleware('permission:player_update_requests.edit')->only(['approve', 'deny']);
        $this->middleware('permission:player_update_requests.create')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        if ($request->query->get('search')) {
            $updateRequests = PlayerUpdateRequest::search($request->query->all());
        } else {
            $updateRequests = PlayerUpdateRequest::with(['player', 'reviewedBy'])
                ->where('status', PlayerUpdateRequest::STATUS_PENDING)
                ->orderBy('id', 'desc')
                ->paginate(config('custom.results_per_page'));
        }

        return view('backoffice.pages.player_update_requests', [
            'updateRequests' => $updateRequests,
            'searchFields' => PlayerUpdateRequest::SEARCH_FIELDS,
            'queryParams' => $request->query->all()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $updateRequest = PlayerUpdateRequest::with(['player', 'reviewedBy'])->findOrFail($id);
        $changes = $updateRequest->getChanges();
        
        // Get teams for the club if club_name is provided
        $clubTeams = [];
        $requiresTeamSelection = false;
        
        if ($updateRequest->club_name && $updateRequest->club_name !== 'no_club' && $updateRequest->club_name !== 'none') {
            $club = \App\Club::where('name', $updateRequest->club_name)->first();
            if ($club) {
                $teams = $club->teams()->where('visible', true)->get();
                if ($teams->count() > 1) {
                    $clubTeams = $teams;
                    $requiresTeamSelection = true;
                }
            }
        }
        
        return view('backoffice.pages.player_update_request', [
            'updateRequest' => $updateRequest,
            'changes' => $changes,
            'clubTeams' => $clubTeams,
            'requiresTeamSelection' => $requiresTeamSelection
        ]);
    }

    /**
     * Approve the update request
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, $id)
    {
        $updateRequest = PlayerUpdateRequest::findOrFail($id);

        // Check if team selection is required
        $requiresTeamSelection = false;
        if ($updateRequest->club_name && $updateRequest->club_name !== 'no_club' && $updateRequest->club_name !== 'none') {
            $club = \App\Club::where('name', $updateRequest->club_name)->first();
            if ($club) {
                $teams = $club->teams()->where('visible', true)->get();
                if ($teams->count() > 1) {
                    $requiresTeamSelection = true;
                }
            }
        }

        // Validate request
        $validationRules = [
            'review_notes' => 'nullable|string|max:1000'
        ];
        
        if ($requiresTeamSelection) {
            $validationRules['team_id'] = 'required|exists:teams,id';
        }

        $request->validate($validationRules);

        if (!$updateRequest->isPending()) {
            $messages = new MessageBag();
            $messages->add('error', 'Esta solicitação já foi processada.');
            return redirect()->back()->with(['popup_message' => $messages]);
        }

        try {
            $isCreateRequest = $updateRequest->isCreateRequest();
            $teamId = $request->input('team_id');
            $updateRequest->approve($request->input('review_notes'), $teamId);

            $messages = new MessageBag();
            if ($isCreateRequest) {
                $messages->add('success', 'Solicitação aprovada e jogador criado com sucesso.');
            } else {
                $messages->add('success', 'Solicitação aprovada e jogador atualizado com sucesso.');
            }

            return redirect()->route('player_update_requests.index')
                           ->with(['popup_message' => $messages]);
        } catch (\Exception $e) {
            $messages = new MessageBag();
            $messages->add('error', 'Erro ao aprovar a solicitação: ' . $e->getMessage());
            return redirect()->back()->with(['popup_message' => $messages]);
        }
    }

    /**
     * Deny the update request
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deny(Request $request, $id)
    {
        $request->validate([
            'review_notes' => 'required|string|max:1000|min:3'
        ]);

        $updateRequest = PlayerUpdateRequest::findOrFail($id);

        if (!$updateRequest->isPending()) {
            $messages = new MessageBag();
            $messages->add('error', 'Esta solicitação já foi processada.');
            return redirect()->back()->with(['popup_message' => $messages]);
        }

        try {
            $updateRequest->deny($request->input('review_notes'));

            $messages = new MessageBag();
            $messages->add('success', 'Solicitação recusada com sucesso.');

            return redirect()->route('player_update_requests.index')
                           ->with(['popup_message' => $messages]);
        } catch (\Exception $e) {
            $messages = new MessageBag();
            $messages->add('error', 'Erro ao recusar a solicitação: ' . $e->getMessage());
            return redirect()->back()->with(['popup_message' => $messages]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $updateRequest = PlayerUpdateRequest::findOrFail($id);
        $oldData = $updateRequest->toArray();
        $updateRequest->delete();

        $messages = new MessageBag();
        $messages->add('success', 'Solicitação de atualização deletada com sucesso.');

        Audit::add(Audit::ACTION_DELETE, 'PlayerUpdateRequest', $oldData);

        return redirect()->route('player_update_requests.index')->with(['popup_message' => $messages]);
    }
}
