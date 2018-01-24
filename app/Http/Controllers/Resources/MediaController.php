<?php

namespace App\Http\Controllers\Resources;

use App\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;

class MediaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:media');
        $this->middleware('permission:media.edit')->except('index');
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $medias = Media::paginate(config('custom.results_per_page'));
        return view('backoffice.pages.media')->with(['medias' => $medias]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_media');
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
            'tags' => 'required|max:255|String',
            'url' => 'required_without:file|max:255|url|nullable',
            'file' => 'required_without:url|file|size:200000|nullable',
            'visible' => 'required',
        ]);

        $errors = new MessageBag();

        if ($request->input('url') != null) {

        }
        else {
            if ($request->file('photo')->isValid()) {

            } else {
                $errors->add('file_invalid', trans('custom_error.file_invalid'));

            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function show(Media $media)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function edit(Media $media)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function destroy(Media $media)
    {
        //
    }
}
