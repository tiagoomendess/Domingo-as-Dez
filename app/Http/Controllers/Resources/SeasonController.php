<?php

namespace App\Http\Controllers\Resources;

use App\GameGroup;
use App\Media;
use App\Season;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;

class SeasonController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:seasons.edit')->only(['edit', 'update']);
        $this->middleware('permission:seasons.create')->only(['create', 'store', 'destroy']);
        $this->middleware('permission:seasons');
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
            'name' => 'nullable|string|max:155',
            'file' => 'nullable|mimes:jpeg,jpg,png|max:20000',
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

        $obs = $request->input('obs');
        $name = $request->input('name') ?: null;
        $picture = null;

        if ($request->hasFile('file')) {
            $picture = $this->storeSeasonPicture($request->file('file'), $name);
            if (!$picture) {
                $errors = new MessageBag();
                $errors->add('file', trans('errors.file_invalid'));
                return redirect()->back()->withErrors($errors)->withInput();
            }
        }

        $season = Season::create([
            'competition_id' => $competition_id,
            'start_year' => $start_year,
            'end_year' => $end_year,
            'name' => $name,
            'picture' => $picture,
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
            'name' => 'nullable|string|max:155',
            'file' => 'nullable|mimes:jpeg,jpg,png|max:20000',
            'clear_picture' => 'nullable',
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

        $obs = $request->input('obs');
        $name = $request->input('name') ?: null;

        if ($request->hasFile('file')) {
            $picture = $this->storeSeasonPicture($request->file('file'), $name);
            if (!$picture) {
                $errors = new MessageBag();
                $errors->add('file', trans('errors.file_invalid'));
                return redirect()->back()->withErrors($errors)->withInput();
            }
            $season->picture = $picture;
        } elseif ($request->input('clear_picture') == 'true') {
            $season->picture = null;
        }

        $season->competition_id = $competition_id;
        $season->start_year = $start_year;
        $season->end_year = $end_year;
        $season->name = $name;
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

    public function getGameGroups($id) {

        $season = Season::findOrFail($id);

        return response()->json($season->game_groups);

    }

    /**
     * Store a season override logo the same way competition logos are stored.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string|null  $name
     * @return string|null
     */
    private function storeSeasonPicture($file, $name = null)
    {
        $originalName = $file->getClientOriginalName();
        $filename = str_random(3) . time() . str_random(6) . '-' . $originalName;

        $url = '/storage/media/images/' . $filename;
        $path = '/public/media/images/';

        try {
            Storage::putFileAs($path, $file, $filename);

            Media::create([
                'url' => $url,
                'media_type' => 'image',
                'tags' => ($name ?: 'season') . ',' . trans('models.season'),
                'user_id' => Auth::user()->id,
                'visible' => true,
            ]);
        } catch (\Exception $e) {
            return null;
        }

        return $url;
    }
}
