<?php

namespace App\Http\Controllers\Resources;

use App\Audit;
use App\Competition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Media;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Storage;
use Auth;

class CompetitionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:competitions')->only(['index', 'show', 'getSeasons']);
        $this->middleware('permission:competitions.edit')->only(['edit', 'update']);
        $this->middleware('permission:competitions.create')->only(['create', 'store', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $competitions = Competition::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.competitions', ['competitions' => $competitions]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_competition');
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
            'name' => 'string|max:155|required|unique:competitions,name',
            'file' => 'required|image|max:20000',
            'visible' => 'required',
        ]);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $name = $request->input('name');

        if ($request->hasFile('file')) {

            $url = MediaController::storeImage(
                $request->file('file'),
                $name . ',' . trans('models.competition')
            );

        } else {

            //melhorar depois
            $messages = new MessageBag();
            $messages->add('error', trans('errors.file_invalid'));
            return redirect()->back();

        }

        $competition = Competition::create([
            'name' => $name,
            'picture' => $url,
            'visible' => $visible
        ]);

        Audit::add(Audit::ACTION_CREATE, 'Competition', null, $competition->toArray());

        return redirect(route('competitions.show', ['competition' => $competition]));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $competition = Competition::findOrFail($id);
        return view('backoffice.pages.competition', ['competition' => $competition]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $competition = Competition::findOrFail($id);

        return view('backoffice.pages.edit_competition', ['competition' => $competition]);
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
            'name' => 'string|max:155|required',
            'file' => 'nullable|image|max:20000',
            'visible' => 'required',
        ]);

        $competition = Competition::findOrFail($id);
        $old_competition = $competition->toArray();
        $messages = new MessageBag();

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $name = $request->input('name');

        if ($request->hasFile('file')) {

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filename = str_random(3) . time() . str_random(6) . '-' . $originalName;

            $url = '/storage/media/images/' . $filename;
            $path = '/public/media/images/';

            Storage::putFileAs(
                $path, $file, $filename
            );

            Media::create([
                'url' => $url,
                'media_type' => 'image',
                'tags' => $name . ',' . trans('models.competition'),
                'user_id' => Auth::user()->id,
                'visible' => true,
            ]);

        } else {
            $url = $competition->picture;
        }

        //alterar valores
        $competition->name = $name;
        $competition->visible = $visible;
        $competition->picture = $url;
        $competition->save();

        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.competition')]));

        Audit::add(Audit::ACTION_UPDATE, 'Competition', $old_competition, $competition->toArray());

        return redirect(route('competitions.show', ['competition' => $competition]))->with(['popup_message' => $messages]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $competition = Competition::findOrFail($id);
        $old_competition = $competition->toArray();

        $competition->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.competition')]));

        Audit::add(Audit::ACTION_DELETE, 'Competition', $old_competition);

        return redirect()->route('competitions.index')->with('popup_message', $messages);
    }

    public function getSeasons($id) {

        $competition = Competition::findOrFail($id);

        return response()->json($competition->seasons);

    }
}
