<?php

namespace App\Http\Controllers\Resources;

use App\Referee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;
use Intervention\Image\Facades\Image;
use App\Media;
use Illuminate\Support\Facades\Auth;

class RefereeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:referees.edit')->only(['edit', 'update']);
        $this->middleware('permission:referees.create')->only(['create', 'store', 'destroy']);
        $this->middleware('permission:referees');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $referees = Referee::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));
        return view('backoffice.pages.referees', ['referees' => $referees]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_referee');
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
            'name' => 'required|string|max:155|min:3',
            'picture' => 'nullable|image',
            'picture_url' => 'nullable|string|max:280|url',
            'association' => 'required|string|max:155',
            'obs' => 'string|max:3000|min:6|nullable',
            'visible' => 'required',
        ]);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $name = $request->input('name');
        $association = $request->input('association');
        $obs = $request->input('obs');

        $image = null;
        $url = null;

        if ($request->hasFile('picture'))
            $image = Image::make($request->file('picture'));
        else if ($request->input('picture_url') != null)
            $image = Image::make($request->input('picture_url'));

        if ($image) {

            $url = MediaController::storeSquareImage($image, $name);

            $tags = trans('models.referee') . ',' . $name;

            Media::create([
                'user_id' => Auth::user()->id,
                'url' => $url,
                'media_type' => 'image',
                'tags' => $tags,
            ]);
        }

        $referee = Referee::create([
            'name' => $name,
            'association' => $association,
            'picture' => $url,
            'obs' => $obs,
            'visible' => $visible,
        ]);

        return redirect(route('referees.show', ['referee' => $referee]));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $referee = Referee::findOrFail($id);
        return view('backoffice.pages.referee', ['referee' => $referee]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $referee = Referee::findOrFail($id);
        return view('backoffice.pages.edit_referee', ['referee' => $referee]);
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
        $referee = Referee::findorFail($id);

        $request->validate([
            'name' => 'required|string|max:155|min:3',
            'picture' => 'nullable|image',
            'picture_url' => 'nullable|string|max:280|url',
            'association' => 'required|string|max:155',
            'obs' => 'string|max:3000|min:6|nullable',
            'visible' => 'required',
        ]);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $name = $request->input('name');
        $association = $request->input('association');
        $obs = $request->input('obs');

        $image = null;

        if ($request->hasFile('picture'))
            $image = Image::make($request->file('picture'));
        else if ($request->input('picture_url') != null)
            $image = Image::make($request->input('picture_url'));

        if ($image) {

            $url = MediaController::storeSquareImage($image, $name);

            $tags = trans('models.referee') . ',' . $name;

            Media::create([
                'user_id' => Auth::user()->id,
                'url' => $url,
                'media_type' => 'image',
                'tags' => $tags,
            ]);

            $referee->picture = $url;
        }

        $referee->name = $name;
        $referee->association = $association;
        $referee->obs = $obs;
        $referee->visible = $visible;
        $referee->save();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.referee')]));

        return redirect(route('referees.show', ['referee' => $referee]))->with(['popup_message' => $messages]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $referee = Referee::findorFail($id);
        $referee->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.referee')]));

        return redirect(route('referees.index'))->with(['popup_message' => $messages]);
    }
}
