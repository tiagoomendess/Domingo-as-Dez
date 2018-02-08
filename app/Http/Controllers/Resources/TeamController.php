<?php

namespace App\Http\Controllers\Resources;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;

class TeamController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:teams');
        $this->middleware('permission:teams.edit')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = Team::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.teams', ['teams' => $teams]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_team');
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
            'club_id' => 'integer|exists:clubs,id|required',
            'name' => 'string|max:155|required',
            'visible' => 'required',
        ]);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $club_id = $request->input('club_id');
        $name = $request->input('name');

        $team = Team::create([
            'name' => $name,
            'club_id' => $club_id,
            'visible' => $visible,
        ]);

        return redirect(route('teams.show', ['team' => $team]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $team = Team::findOrFail($id);
        return view('backoffice.pages.team', ['team' => $team]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $team = Team::findOrFail($id);
        return view('backoffice.pages.edit_team', ['team' => $team]);
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
            'club_id' => 'integer|exists:clubs,id|required',
            'name' => 'string|max:155|required',
            'visible' => 'required',
        ]);

        $team = Team::findOrFail($id);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $club_id = $request->input('club_id');
        $name = $request->input('name');

        $team->name = $name;
        $team->club_id = $club_id;
        $team->visible = $visible;
        $team->save();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.team')]));

        return redirect(route('teams.show', ['team' => $team]))->with(['popup_message' => $messages]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.team')]));

        return redirect(route('teams.index'))->with(['popup_message' => $messages]);
    }
}
