<?php

namespace App\Http\Controllers\Resources;

use App\Club;
use App\Game;
use App\GameGroup;
use App\GameReferee;
use App\Goal;
use App\Playground;
use App\Season;
use App\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class GameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:games')->only(['index', 'show', 'getTeams']);
        $this->middleware('permission:games.edit')->only(['edit', 'update']);
        $this->middleware('permission:games.create')->only(['create', 'store', 'destroy', 'showImportPage', 'importGames']);
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
        return view('backoffice.pages.create_game', ['user' => Auth::user()]);
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
            'game_group_id' => 'required|integer|exists:game_groups,id',
            'date' => 'required|date',
            'hour' => ["required", "string", "regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/"],
            'round' => 'required|integer|min:0|max:9999',
            'goals_home' => 'nullable|integer|min:0|max:99',
            'goals_away' => 'nullable|integer|min:0|max:99',
            'penalties_home' => 'nullable|integer|min:0|max:99',
            'penalties_away' => 'nullable|integer|min:0|max:99',
            'playground_id' => 'nullable|integer|exists:playgrounds,id',
            'referees_id' => 'nullable|array|min:1|max:8',
            'referees_id.*' => 'required|integer|exists:referees,id',
            'types_id' => 'nullable|array|min:1|max:8',
            'types_id.*' => 'required|integer|exists:referee_types,id',
            'timezone' => 'required|string|max:20'
        ]);

        //dd($request->input('timezone'));

        $carbon = new Carbon($request->input('date'), $request->input('timezone'));
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
        $penalties_home = $request->input('penalties_home');
        $penalties_away = $request->input('penalties_away');
        $game_group_id = $request->input('game_group_id');
        $round = $request->input('round');
        $playground_id = $request->input('playground_id');

        $game = Game::create([

            'home_team_id' => $home_team_id,
            'away_team_id' => $away_team_id,
            'game_group_id' => $game_group_id,
            'visible' => $visible,
            'finished'=> $finished,
            'goals_home' => $goals_home,
            'goals_away' => $goals_away,
            'penalties_home' => $penalties_home,
            'penalties_away' => $penalties_away,
            'round' => $round,
            'date' => Carbon::createFromTimestamp($carbon->timestamp)->format("Y-m-d H:i:s"),
            'playground_id' => $playground_id,

        ]);

        //If there were referees set
        if ($request->input('referees_id') && $request->input('types_id')) {

            $referees_id = $request->input('referees_id');
            $types_id = $request->input('types_id');

            if (count($referees_id) != count($types_id))
                return redirect(route('games.show', ['game' => $game]));

            $total_refs = count($referees_id);

            for ($i = 0; $i < $total_refs; $i++) {

                GameReferee::create([
                    'game_id' => $game->id,
                    'referee_id' => $referees_id[$i],
                    'referee_type_id' => $types_id[$i],
                ]);
            }

        }

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
            'game_group_id' => 'required|integer|exists:game_groups,id',
            'date' => 'required|date',
            'hour' => ["required", "string", "regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/"],
            'round' => 'required|integer|min:0|max:9999',
            'goals_home' => 'nullable|integer|min:0|max:99',
            'goals_away' => 'nullable|integer|min:0|max:99',
            'penalties_home' => 'nullable|integer|min:0|max:99',
            'penalties_away' => 'nullable|integer|min:0|max:99',
            'playground_id' => 'nullable|integer|exists:playgrounds,id',
            'referees_id' => 'nullable|array|min:1|max:8',
            'referees_id.*' => 'required|integer|exists:referees,id',
            'types_id' => 'nullable|array|min:1|max:8',
            'types_id.*' => 'required|integer|exists:referee_types,id',
            'timezone' => 'required|string|max:20',
        ]);

        $game = Game::findOrFail($id);

        $carbon = new Carbon($request->input('date'), $request->input('timezone'));
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
        $penalties_home = $request->input('penalties_home');
        $penalties_away = $request->input('penalties_away');
        $game_group_id = $request->input('game_group_id');
        $round = $request->input('round');
        $playground_id = $request->input('playground_id');

        $game->home_team_id = $home_team_id;
        $game->away_team_id = $away_team_id;
        $game->goals_home = $goals_home;
        $game->goals_away = $goals_away;
        $game->penalties_home = $penalties_home;
        $game->penalties_away = $penalties_away;
        $game->game_group_id = $game_group_id;
        $game->round = $round;
        $game->playground_id = $playground_id;
        $game->date = Carbon::createFromTimestamp($carbon->timestamp)->format("Y-m-d H:i:s");
        $game->visible = $visible;
        $game->finished = $finished;

        $game->save();

        $game_referees = $game->game_referees;

        //If there were referees set
        if ($request->input('referees_id') && $request->input('types_id')) {

            $referees_id = $request->input('referees_id');
            $types_id = $request->input('types_id');

            if (count($referees_id) != count($types_id))
                return redirect(route('games.show', ['game' => $game]));

            $total_refs_form = count($referees_id);

            foreach ($game_referees as $game_referee) {

                $match = false;

                for ($i = 0; $i < $total_refs_form; $i++) {

                    if ($game_referee->id == $referees_id[$i] && $game_referee->referee_type->id == $types_id[$i]) {
                        $match = true;
                        break;
                    }

                }

                if (!$match)
                    $game_referee->delete();

            }

            for ($i = 0; $i < $total_refs_form; $i++) {

                $match = false;

                foreach ($game_referees as $game_referee) {

                    if ($game_referee->id == $referees_id[$i] && $game_referee->referee_type->id == $types_id[$i]) {
                        $match = true;
                        break;
                    }

                }

                if (!$match) {

                    GameReferee::create([
                        'game_id' => $game->id,
                        'referee_id' => $referees_id[$i],
                        'referee_type_id' => $types_id[$i],
                    ]);

                }

            }

        } else {

            foreach ($game_referees as $game_referee)
                $game_referee->delete();

        }

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

    public function showImportPage() {
        return view("backoffice.pages.import_games");
    }

    public function importGames(Request $request) {

        $request->validate([
            'import_file' => 'required|file|max:2000',
            'team_name' => 'required|string|max:155|min:3',
            'game_group_id' => 'required|integer|exists:game_groups,id',
        ]);

        $game_group = GameGroup::find($request->input('game_group_id'));
        $team_name = $request->input('team_name');
        $now = Carbon::now();
        $nowMinus2H = $now->subHours(2);

        $games_created = 0;
        $goals_added = 0;
        $clubs_created = 0;
        $teams_created = 0;
        $playgrounds_created = 0;
        $lines = 0;

        if (($handle = fopen ($request->file('import_file'), 'r' )) !== FALSE) {

            while ( ($data = fgetcsv ( $handle, 1000, ';' )) !== FALSE ) {

                $lines++;

                $date = Carbon::createFromFormat("Y-m-d H:i:s", $data[1], 'Europe/Lisbon');

                $home_club = Club::where('name', 'like', '%' . $data[2] . '%')->first();

                if ($home_club == null) {
                    $home_club = Club::create([
                        'name' => $data[2],
                    ]);

                    $clubs_created++;
                }

                $home_club_teams = $home_club->teams;
                $home_team = null;

                foreach ($home_club_teams as $team) {

                    if ($team->name == $team_name) {
                        $home_team = $team;
                        break;
                    }

                }

                if(!$home_team) {

                    $home_team = Team::create([
                        'club_id' => $home_club->id,
                        'name' => $team_name,
                    ]);

                    $teams_created++;

                }

                $away_club = Club::where('name', 'like', '%' . $data[4] . '%')->first();

                if ($away_club == null) {

                    $away_club = Club::create([
                        'name' => $data[4],
                    ]);

                    $clubs_created++;
                }

                $away_club_teams = $away_club->teams;
                $away_team = null;

                foreach ($away_club_teams as $team) {

                    if ($team->name == $team_name) {
                        $away_team = $team;
                        break;
                    }

                }

                if(!$away_team) {

                    $away_team = Team::create([
                        'club_id' => $away_club->id,
                        'name' => $team_name,
                    ]);

                    $teams_created++;

                }

                $playground = $home_club->getFirstPlayground();

                if(!$playground) {

                    $playgrounds_created++;

                    $playground = Playground::create([
                        'club_id' => $home_club->id,
                        'name' => 'Campo de ' . $home_club->name,
                        'surface' => 'Pelado',
                    ]);
                }

                $round = $data[0];

                $game = Game::where('game_group_id', $game_group->id)->where('home_team_id', $home_team->id)->where('away_team_id', $away_team->id)->where('round', $round)->first();

                if (!$game) {

                    $games_created++;

                    if($nowMinus2H->timestamp > $date->timestamp)
                        $finished = true;
                    else
                        $finished = false;

                    $game = Game::create([
                        'home_team_id' => $home_team->id,
                        'away_team_id' => $away_team->id,
                        'game_group_id' => $game_group->id,
                        'round' => $round,
                        'date' => Carbon::createFromTimestamp($date->timestamp)->format("Y-m-d H:i:s"),
                        'playground_id' => $playground->id,
                        'finished' => $finished,
                        'tie' => $data[6],
                        'table_group' => $data[7],
                        'visible' => true,
                    ]);
                }

                if (!$game->goals_home && !$game->goals_away) {

                    for ($i = $game->getTotalHomeGoals(); $i < $data[3]; $i++) {
                        Goal::create([
                            'team_id' => $home_team->id,
                            'game_id' => $game->id,
                            'minute' => rand(1, 90),
                        ]);
                        $goals_added++;
                    }

                    for ($i = $game->getTotalAwayGoals(); $i < $data[5]; $i++) {
                        Goal::create([
                            'team_id' => $away_team->id,
                            'game_id' => $game->id,
                            'minute' => rand(1, 90),
                        ]);
                        $goals_added++;
                    }
                }

            }

            fclose ( $handle );
        }

        $messages = new MessageBag();

        $messages->add('success', 'Foram lidas ' . $lines . ' linhas, criados ' . $clubs_created . ' clubes,' . $teams_created . ' equipas, ' . $games_created . ' jogos e adicionados ' . $goals_added . ' golos.');
        return redirect()->route('games.show_import_page')->with(['popup_message' => $messages]);

    }
}
