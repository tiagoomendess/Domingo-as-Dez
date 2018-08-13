<?php

namespace App\Http\Controllers\Resources;

use App\Game;
use App\Goal;
use App\Player;
use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;

class GoalController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:goals.edit')->only(['edit', 'update']);
        $this->middleware('permission:goals.create')->only(['create', 'store', 'destroy']);
        $this->middleware('permission:goals');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $goals = Goal::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.goals', ['goals' => $goals]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        if ($request->query('game_id')) {

            $game = Game::find($request->query('game_id'));

        }
        else {
            $game = null;
        }

        return view('backoffice.pages.create_goal', ['game' => $game]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'game_id' => 'required|integer|exists:games,id',
            'player_id' => 'nullable|integer|exists:players,id',
            'selected_team_id' => 'required|integer|exists:teams,id',
            'own_goal' => 'required|string',
            'visible' => 'required|string',
            'penalty' => 'required|string',
            'minute' => 'nullable|integer|min:1|max:140',
        ]);

        if($request->input('own_goal') == 'true')
            $own_goal = true;
        else
            $own_goal = false;

        if($request->input('penalty') == 'true')
            $penalty = true;
        else
            $penalty = false;

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $game = Game::find($request->input('game_id'));

        $team = Team::find($request->input('selected_team_id'));
        $minute = $request->input('minute');

        if ($request->input('player_id')) {

            $player = Player::find($request->input('player_id'));

            if($team->id != $player->getTeam()->id)
                $own_goal = true;
        }
        else
            $player = null;


        $goal = Goal::create([
            'game_id' => $game->id,
            'player_id' => $request->input('player_id'),
            'team_id' => $team->id,
            'own_goal' => $own_goal,
            'penalty' => $penalty,
            'visible' => $visible,
            'minute' => $minute,
        ]);

        return redirect(route('goals.show', ['goal' => $goal]));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $goal = Goal::findOrFail($id);
        return view('backoffice.pages.goal', ['goal' => $goal]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $goal = Goal::findOrFail($id);
        return view('backoffice.pages.edit_goal', ['goal' => $goal]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'game_id' => 'required|integer|exists:games,id',
            'player_id' => 'nullable|integer|exists:players,id',
            'selected_team_id' => 'required|integer|exists:teams,id',
            'own_goal' => 'required|string',
            'visible' => 'required|string',
            'penalty' => 'required|string',
            'minute' => 'nullable|integer|min:1|max:140',
        ]);

        $goal = Goal::findOrFail($id);

        if($request->input('own_goal') == 'true')
            $own_goal = true;
        else
            $own_goal = false;

        if($request->input('penalty') == 'true')
            $penalty = true;
        else
            $penalty = false;

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $game = Game::find($request->input('game_id'));

        $team = Team::find($request->input('selected_team_id'));
        $minute = $request->input('minute');

        if ($request->input('player_id')) {

            $player = Player::find($request->input('player_id'));

            if($team->id != $player->getTeam()->id)
                $own_goal = true;
        }
        else
            $player = null;

        $goal->game_id = $game->id;
        $goal->player_id = $request->input('player_id');
        $goal->team_id = $team->id;
        $goal->minute = $minute;
        $goal->penalty = $penalty;
        $goal->own_goal = $own_goal;
        $goal->visible = $visible;

        $goal->save();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.goal')]));

        return redirect(route('goals.show', ['goal' => $goal]))->with(['popup_message' => $messages]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $goal = Goal::findOrFail($id);
        $goal->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.goal')]));

        return redirect(route('goals.index'))->with(['popup_message' => $messages]);
    }
}
