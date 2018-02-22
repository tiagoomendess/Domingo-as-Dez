<?php

namespace App\Http\Controllers\Resources;

use App\Game;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;

class GameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:games');
        $this->middleware('permission:games.edit')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $games = Game::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.games', ['games' => $games]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_game');
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
            'visible' => 'required',
            'finished' => 'required',
            'home_team_id' => 'required|integer|exists:teams,id',
            'away_team_id' => 'required|integer|exists:teams,id',
            'season_id' => 'required|integer|exists:seasons,id',
            'date' => 'required|date',
            'hour' => ["required", "string", "regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/"],
            'round' => 'required|integer|min:0|max:9999',
            'home_goals' => 'nullable|integer|min:0|max:99',
            'away_goals' => 'nullable|integer|min:0|max:99',
            'playground_id' => 'nullable|integer|exists:playgrounds,id'
        ]);

        $carbon = new Carbon($request->input('date'));
        $splited = explode(':', $request->input('hour'));

        $carbon->addHours($splited[0]);
        $carbon->addMinutes($splited[1]);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        if($request->input('finished') == 'true')
            $finished = true;
        else
            $finished = false;

        $home_team_id = $request->input('home_team_id');
        $away_team_id = $request->input('away_team_id');
        $goals_home = $request->input('goals_home');
        $goals_away = $request->input('goals_away');
        $season_id = $request->input('season_id');
        $round = $request->input('round');
        $playground_id = $request->input('playground_id');

        $game = Game::create([

            'home_team_id' => $home_team_id,
            'away_team_id' => $away_team_id,
            'season_id' => $season_id,
            'visible' => $visible,
            'finished'=> $finished,
            'goals_home' => $goals_home,
            'goals_away' => $goals_away,
            'round' => $round,
            'date' => $carbon->format("Y-m-d H:i:s"),
            'playground_id' => $playground_id,

        ]);

        return redirect(route('games.show', ['game' => $game]));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $game = Game::findOrFail($id);
        return view('backoffice.pages.game', ['game' => $game]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $game = Game::findOrFail($id);
        return view('backoffice.pages.edit_game', ['game' => $game]);
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
            'visible' => 'required',
            'finished' => 'required',
            'home_team_id' => 'required|integer|exists:teams,id',
            'away_team_id' => 'required|integer|exists:teams,id',
            'season_id' => 'required|integer|exists:seasons,id',
            'date' => 'required|date',
            'hour' => ["required", "string", "regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/"],
            'round' => 'required|integer|min:0|max:9999',
            'home_goals' => 'nullable|integer|min:0|max:99',
            'away_goals' => 'nullable|integer|min:0|max:99',
            'playground_id' => 'nullable|integer|exists:playgrounds,id'
        ]);

        $game = Game::findOrFail($id);

        $carbon = new Carbon($request->input('date'));
        $splited = explode(':', $request->input('hour'));

        $carbon->addHours($splited[0]);
        $carbon->addMinutes($splited[1]);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        if($request->input('finished') == 'true')
            $finished = true;
        else
            $finished = false;

        $home_team_id = $request->input('home_team_id');
        $away_team_id = $request->input('away_team_id');
        $goals_home = $request->input('goals_home');
        $goals_away = $request->input('goals_away');
        $season_id = $request->input('season_id');
        $round = $request->input('round');
        $playground_id = $request->input('playground_id');


        $game->home_team_id = $home_team_id;
        $game->away_team_id = $away_team_id;
        $game->goals_home = $goals_home;
        $game->goals_away = $goals_away;
        $game->season_id = $season_id;
        $game->round = $round;
        $game->playground_id = $playground_id;
        $game->date = $carbon->format("Y-m-d H:i:s");
        $game->visible = $visible;
        $game->finished = $finished;

        $game->save();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.game')]));

        return redirect(route('games.show', ['game' => $game]))->with(['popup_message' => $messages]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.game')]));

        return redirect(route('games.index'))->with(['popup_message' => $messages]);
    }

    public function getTeams($id) {

        $game = Game::findOrFail($id);

        $game->homeTeam->club;
        $game->awayTeam->club;

        $teams[0] = $game->homeTeam;
        $teams[1] = $game->awayTeam;

        return response()->json($teams);
    }
}
