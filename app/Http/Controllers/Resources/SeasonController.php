<?php

namespace App\Http\Controllers\Resources;

use App\Season;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;

class SeasonController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:seasons');
        $this->middleware('permission:seasons.edit')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seasons = Season::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.seasons', ['seasons' => $seasons]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_season');
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

            'competition' => 'integer|required|exists:competitions,id',
            'start_year' => 'required|integer|min:1970|max:20000',
            'end_year' => 'required|integer|min:1970|max:20000',
            'relegates' => 'required|integer|min:0|max:30',
            'promotes' => 'required|integer|min:0|max:30',
            'obs' => 'nullable|string|max:60000',
            'visible' => 'required',

        ]);

        $competition_id = $request->input('competition');
        $start_year = $request->input('start_year');
        $end_year = $request->input('end_year');

        if ($start_year > $end_year) {

            $errors = new MessageBag();
            $errors->add('x_bigger_than_y', trans('errors.x_bigger_than_y', [
                'x' => trans('models.start_year'),
                'y' => trans('models.end_year')
            ]));

            return redirect()->back()->withErrors($errors);
        }

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $relegates = $request->input('relegates');
        $promotes =  $request->input('promotes');
        $obs = $request->input('obs');

        $season = Season::create([
            'competition_id' => $competition_id,
            'start_year' => $start_year,
            'end_year' => $end_year,
            'relegates' => $relegates,
            'promotes' => $promotes,
            'obs' => $obs,
            'visible' => $visible,
        ]);

        return redirect(route('seasons.show', ['season' => $season]));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $season = Season::findOrFail($id);

        return view('backoffice.pages.season', ['season' => $season]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $season = Season::findOrFail($id);

        return view('backoffice.pages.edit_season', ['season' => $season]);
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

            'competition' => 'integer|required|exists:competitions,id',
            'start_year' => 'required|integer|min:1970|max:20000',
            'end_year' => 'required|integer|min:1970|max:20000',
            'relegates' => 'required|integer|min:0|max:30',
            'promotes' => 'required|integer|min:0|max:30',
            'obs' => 'nullable|string|max:60000',
            'visible' => 'required',

        ]);

        $season = Season::findOrFail($id);

        $competition_id = $request->input('competition');
        $start_year = $request->input('start_year');
        $end_year = $request->input('end_year');

        if ($start_year > $end_year) {

            $errors = new MessageBag();
            $errors->add('x_bigger_than_y', trans('errors.x_bigger_than_y', [
                'x' => trans('models.start_year'),
                'y' => trans('models.end_year')
            ]));

            return redirect()->back()->withErrors($errors);
        }

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $relegates = $request->input('relegates');
        $promotes =  $request->input('promotes');
        $obs = $request->input('obs');

        $season->competition_id = $competition_id;
        $season->start_year = $start_year;
        $season->end_year = $end_year;
        $season->promotes = $promotes;
        $season->relegates = $relegates;
        $season->obs = $obs;
        $season->visible = $visible;
        $season->save();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.season')]));

        return redirect(route('seasons.show', ['season' => $season]))->with(['popup_message' => $messages]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $season = Season::findOrFail($id);

        $season->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.season')]));

        return redirect(route('seasons.index'))->with(['popup_message' => $messages]);
    }

    public function getGames($id) {

        $season = Season::findOrFail($id);

        $games = $season->games;

        foreach ($games as $game) {

            $dummy = $game->homeTeam;
            $dummy = $game->homeTeam->club;
            $dummy = $game->awayTeam;
            $dummy = $game->awayTeam->club;
        }

        return response()->json($games);
    }
}