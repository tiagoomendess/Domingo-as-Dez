<?php

namespace App\Http\Controllers\Resources;

use App\GameGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;

class GameGroupController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:game_groups');
        $this->middleware('permission:game_groups.edit')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $game_groups = GameGroup::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));
        return view('backoffice.pages.game_groups', ['game_groups' => $game_groups]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_game_group');
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
            'name' => 'string|max:154|required',
            'season_id' => 'required|exists:seasons,id',
            'group_rules_id' => 'required|exists:group_rules,id'
        ]);

        $name = $request->input('name');
        $season_id = $request->input('season_id');
        $group_rules_id = $request->input('group_rules_id');

        $game_group = GameGroup::create([
            'name' => $name,
            'season_id' => $season_id,
            'group_rules_id' => $group_rules_id,
        ]);

        return redirect()->route('gamegroups.show', ['game_group' => $game_group]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $game_group = GameGroup::findOrFail($id);
        return view('backoffice.pages.game_group', ['game_group' => $game_group]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $game_group = GameGroup::findOrFail($id);
        return view('backoffice.pages.edit_game_group', ['game_group' => $game_group]);
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
        $game_group = GameGroup::findOrFail($id);

        $request->validate([
            'name' => 'string|max:154|required',
            'season_id' => 'required|exists:seasons,id',
            'group_rules_id' => 'required|exists:group_rules,id'
        ]);

        $name = $request->input('name');
        $season_id = $request->input('season_id');
        $group_rules_id = $request->input('group_rules_id');

        $game_group->name = $name;
        $game_group->season_id = $season_id;
        $game_group->group_rules_id = $group_rules_id;

        $game_group->save();

        return redirect()->route('gamegroups.show', ['game_group' => $game_group]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $game_group = GameGroup::findOrFail($id);
        $game_group->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.game_group')]));

        return redirect(route('gamegroups.index'))->with(['popup_message' => $messages]);
    }

    public function getGames($id) {

        $game_group = GameGroup::findOrFail($id);

        $games = $game_group->games;

        foreach ($games as $game) {

            $dummy = $game->homeTeam;
            $dummy = $game->homeTeam->club;
            $dummy = $game->awayTeam;
            $dummy = $game->awayTeam->club;
        }

        return response()->json($games);
    }
}
