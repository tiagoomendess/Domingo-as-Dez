<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Page;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use Intervention\Image\Facades\Image;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:pages')->only(['index', 'show']);
        $this->middleware('permission:pages.edit')->only(['edit', 'update']);
        $this->middleware('permission:pages.create')->only(['create', 'store', 'destroy']);
    }

    /**
     * Display a listing of the resource
     *
     * @returns View
     */
    public function index()
    {
        $pages = Page::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.pages', [
            'pages' => $pages
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backoffice.pages.create_page');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:155|string',
            'name' => 'required|max:50|string',
            'body' => 'required|min:1',
            'picture' => 'nullable|mimes:jpeg,jpg,png|max:20000',
            'visible' => 'required',
        ]);

        $picture = '';
        if ($request->hasFile('picture')) {
            $picture = MediaController::storeImage(
                $request->file('picture'),
                str_replace(' ', ',', $request->input('title'))
            );
        }

        $page = Page::create([
            'name' => $request->input('name'),
            'title' => $request->input('title'),
            'slug' => str_slug($request->input('title')),
            'body' => $request->input('body'),
            'visible' => $request->input('visible') == 'true',
            'picture' => $picture
        ]);

        return redirect(route('pages.show', ['page' => $page]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        $page = Page::findOrFail($id);
        return view('backoffice.pages.page', ['page' => $page]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('backoffice.pages.edit_page', ['page' => $page]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:155|string',
            'name' => 'required|max:50|string',
            'body' => 'required|min:1',
            'picture' => 'nullable|file|mimes:jpeg,jpg,png',
            'visible' => 'required',
        ]);

        $page = Page::findOrFail($id);

        $page->title = $request->input('title');
        $page->name = $request->input('name');
        $page->body = $request->input('body');
        $page->visible = $request->input('visible') == 'true';
        $page->slug = str_slug($page->title);

        $page->save();

        return redirect(route('pages.show', ['page' => $page]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $page = Page::findOrFail($id);

        $page->delete();

        $message = new MessageBag();
        $message->add('success', trans('success.model_deleted', ['model_name' => trans('models.page')]));

        return redirect(route('pages.index'))->with(['popup_message' => $message]);
    }
}
