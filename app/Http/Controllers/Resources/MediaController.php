<?php

namespace App\Http\Controllers\Resources;

use App\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;

class MediaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:media');
        $this->middleware('permission:media.edit')->except('index');
    }

    public function getMediaFromOutside($filename) {

    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $medias = Media::paginate(config('custom.results_per_page'));
        return view('backoffice.pages.medias')->with(['medias' => $medias]);
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
            'file' => 'nullable|required_without:url|file|max:200000',
            'visible' => 'required',
        ]);

        $errors = new MessageBag();
        $url = $request->input('url');
        $tags = str_replace(', ', ',', $request->input('tags'));
        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        if ($url != null) {

            $mediaType = 'other';

            if (str_contains($url, ['youtube.com', 'youtu.be', 'youtube']))
                $mediaType = 'youtube';

            if (str_contains($url, ['.jpeg', '.png', '.gif']))
                $mediaType = 'image';

            if (str_contains($url, ['.mp4', '.avi', '.mpeg']))
                $mediaType = 'video';

            $media = Media::create([
                'url' => $url,
                'media_type' => $mediaType,
                'tags' => $tags,
                'user_id' => Auth::user()->id,
                'visible' => $visible,
            ]);
        }
        else {

            if ($request->hasFile('file')) {

                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();
                $originalName = $file->getClientOriginalName();
                $filename = str_random(3) . time() . str_random(6) . '-' . $originalName;

                $mediaType = 'other';
                $folder = 'files';

                if (str_contains($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
                    $mediaType = 'image';
                    $folder = 'images';
                }

                if (str_contains($extension, ['mp4', 'avi'])) {
                    $mediaType = 'video';
                    $folder = 'videos';
                }

                $url = '/storage/media/' . $folder . '/' . $filename;
                $path = '/public/media/' . $folder;

                Storage::putFileAs(
                    $path, $file, $filename
                );

                $media = Media::create([
                    'url' => $url,
                    'media_type' => $mediaType,
                    'tags' => $tags,
                    'user_id' => Auth::user()->id,
                    'visible' => $visible,
                ]);

            } else {
                //melhorar depois
                return redirect()->back();
            }
        }

        return redirect()->route('media.show', ['media' => $media]);
    }

    /**
     * Display the specified resource.
     *

     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $media = Media::findOrFail($id);
        return view('backoffice.pages.media', ['media' => $media]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $media = Media::findOrFail($id);
        return view('backoffice.pages.edit_media', ['media' => $media]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $media = Media::findOrFail($id);

        $request->validate([
            'tags' => 'required|max:255|String',
            'visible' => 'required',
        ]);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $media->tags = str_replace(', ', ',', $request->input('tags'));
        $media->visible = $visible;
        $media->save();

        return redirect(route('media.show', ['media' => $media]));
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
