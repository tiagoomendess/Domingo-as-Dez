<?php

namespace App\Http\Controllers\Resources;

use App\Playground;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;

class PlaygroundController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:playgrounds.edit')->only(['edit', 'update']);
        $this->middleware('permission:playgrounds.create')->only(['create', 'store', 'destroy']);
        $this->middleware('permission:playgrounds');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->query->get('search')) {
            $playgrounds = Playground::search($request->query->all());
        } else {
            $playgrounds = Playground::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));
        }

        return view('backoffice.pages.playgrounds', [
            'playgrounds' => $playgrounds,
            'searchFields' => Playground::SEARCH_FIELDS,
            'queryParams' => $request->query->all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_playground');
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
            'club_id' => 'nullable|integer|exists:clubs,id',
            'surface' => 'string|required|max:155',
            'width' => 'nullable|integer|max:200|min:2',
            'height' => 'nullable|integer|max:200|min:5',
            'capacity' => 'nullable|integer|max:200000|min:0',
            'picture' => 'nullable|mimes:jpeg,jpg,png|max:20000',
            'obs' => 'nullable|string|max:1000|min:1',
            'visible' => 'required',
            'priority' => 'integer|min:0|max:100|nullable',
        ]);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $name = $request->input('name');
        $club_id = $request->input('club_id');
        $surface = $request->input('surface');
        $width = $request->input('width');
        $height = $request->input('height');
        $capacity = $request->input('capacity');
        $priority = $request->input('priority', 0);
        $obs = $request->input('obs');

        if($request->hasFile('picture')) {

            $url = MediaController::storeImage($request->file('picture'),
                trans('models.playground') . ',' . $name
            );

        } else
            $url = null;

        $playground = Playground::create([
            'club_id' => $club_id,
            'name' => $name,
            'surface' => $surface,
            'width' => $width,
            'height' => $height,
            'capacity' => $capacity,
            'obs' => $obs,
            'picture' => $url,
            'visible' => $visible,
            'priority' => $priority,
        ]);

        return redirect(route('playgrounds.show', ['playground' => $playground]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $playground = Playground::findOrFail($id);
        return view('backoffice.pages.playground', ['playground' => $playground]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $playground = Playground::findOrFail($id);

        return view('backoffice.pages.edit_playground', [
            'playground' => $playground,
            'latitude' => $playground->getLatitude(),
            'longitude' => $playground->getLongitude(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:155',
            'club_id' => 'nullable|integer|exists:clubs,id',
            'surface' => 'string|required|max:155',
            'width' => 'nullable|integer|max:200|min:2',
            'height' => 'nullable|integer|max:200|min:5',
            'capacity' => 'nullable|integer|max:200000|min:0',
            'picture' => 'nullable|mimes:jpeg,jpg,png|max:20000',
            'obs' => 'nullable|string|max:1000|min:1',
            'visible' => 'required',
            'address_latitude' => 'numeric|nullable',
            'address_longitude' => 'numeric|nullable',
            'priority' => 'integer|min:0|max:100|nullable',
        ]);

        $playground = Playground::findOrFail($id);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $name = $request->input('name');
        $club_id = $request->input('club_id');
        $surface = $request->input('surface');
        $width = $request->input('width');
        $height = $request->input('height');
        $capacity = $request->input('capacity');
        $obs = $request->input('obs');
        $priority = $request->input('priority', 0);

        if($request->hasFile('picture')) {

            $url = MediaController::storeImage($request->file('picture'),
                trans('models.playground') . ',' . $name
            );

            $playground->picture = $url;

        }

        $playground->name = $name;
        $playground->club_id = $club_id;
        $playground->surface = $surface;
        $playground->width = $width;
        $playground->height = $height;
        $playground->capacity = $capacity;
        $playground->obs = $obs;
        $playground->visible = $visible;
        $playground->priority = $priority;
        $playground->location = $playground->toPoint($request->input('address_latitude'), $request->input('address_longitude'));
        $playground->save();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.playground')]));

        return redirect(route('playgrounds.show', ['playground' => $playground]))->with(['popup_message' => $messages]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $playground = Playground::findOrFail($id);

        $playground->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.playground')]));

        return redirect(route('playgrounds.index'))->with(['popup_message' => $messages]);

    }
}
