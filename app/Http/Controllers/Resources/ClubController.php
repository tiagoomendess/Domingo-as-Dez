<?php

namespace App\Http\Controllers\Resources;

use App\Club;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;

class ClubController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:clubs');
        $this->middleware('permission:clubs.edit')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clubs = Club::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.clubs', ['clubs' => $clubs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_club');
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

            'name' => 'required|string|max:155',
            'emblem' => 'nullable|file|mimes:png,svg|max:1000',
            'website' => 'string|max:280|nullable|url',
            'visible' => 'required',

        ]);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $name = $request->input('name');
        $website = $request->input('website');

        if($request->hasFile('emblem')) {
            $url = MediaController::storeImage($request->file('emblem'),
             trans('models.emblem') . ',' . trans('models.club') . ',' . $name
             );
        } else {
            $url = null;
        }

        $club = Club::create([
            'name' => $name,
            'website' => $website,
            'emblem' => $url,
            'visible' => $visible,
        ]);

        return redirect(route('clubs.show', ['club' => $club]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $club = Club::findOrFail($id);

        return view('backoffice.pages.club', ['club' => $club]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $club = Club::findOrFail($id);

        return view('backoffice.pages.edit_club', ['club' => $club]);
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

            'name' => 'required|string|max:155',
            'emblem' => 'nullable|file|mimes:png,svg|max:1000',
            'website' => 'string|max:280|nullable|url',
            'visible' => 'required',

        ]);

        $club = Club::findOrFail($id);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $name = $request->input('name');
        $website = $request->input('website');

        if($request->hasFile('emblem')) {
            $url = MediaController::storeImage($request->file('emblem'),
                trans('models.emblem') . ',' . trans('models.club') . ',' . $name
            );
        } else {
            $url = $club->emblem;
        }

        $club->name = $name;
        $club->emblem = $url;
        $club->website = $website;
        $club->visible = $visible;
        $club->save();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.club')]));

        return redirect(route('clubs.show', ['club' => $club]))->with(['popup_message' => $messages]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $club = Club::findOrFail($id);
        $club->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.club')]));

        return redirect(route('clubs.index'))->with(['popup_message' => $messages]);
    }
}
