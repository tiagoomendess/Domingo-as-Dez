<?php

namespace App\Http\Controllers\Resources;

use App\Media;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\DB;

class MediaController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth');
        $this->middleware('permission:media');
        $this->middleware('permission:media.edit')->except(['index', 'show']);
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

    public function mediaQuery(Request $request) {

        $request->validate([
            'tags' => 'nullable|string|max:155'
        ]);

        if ($request->input('tags') == null)
            $tags = 'all';

        $tags = str_replace(', ', ',', $request->input('tags'));
        $tags_array = explode(',', $tags);

        if($tags == 'all') {
            $medias = Media::where('visible', true)->orderBy('id', 'desc')->limit(config('custom.results_per_page'))->get();
        } else {

            $medias = DB::table('media')->where('tags', 'like', '%' . $tags_array[0] . '%');

            for($i = 1; $i < count($tags_array); $i++) {
                $medias = $medias->orWhere('tags', 'like', '%' . $tags_array[$i] . '%');
            }

            $medias = $medias->where('visible', true)->orderBy('id', 'desc')->limit(config('custom.results_per_page'))->get();
        }

        return response()->json(['response' => $medias]);
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
                $originalName = str_slug($file->getClientOriginalName());
                $filename = str_random(3) . time() . str_random(6) . '_' . $originalName;

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

        $message = new MessageBag();
        $message->add('success', trans('success.model_added', ['model_name' => trans('models.media')]));

        return redirect()->route('media.show', ['media' => $media])->with(['popup_message' => $message]);
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

        $user = Auth::user();
        if ($user->id != $media->user_id) {
            if(!$user->hasPermission('admin')) {

                $error = new MessageBag();
                $error->add('error', trans('errors.no_permission'));

                return redirect(route('media.show', ['media' => $media]))->with(['popup_message' => $error]);
            }
        }

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $media->tags = str_replace(', ', ',', $request->input('tags'));
        $media->visible = $visible;
        $media->save();

        $message = new MessageBag();
        $message->add('success', trans('success.model_edited', ['model_name' => trans('models.media')]));

        return redirect(route('media.show', ['media' => $media]))->with('popup_message', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $media = Media::findOrFail($id);

        $user = Auth::user();
        if ($user->id != $media->user_id) {
            if(!$user->hasPermission('admin')) {

                $error = new MessageBag();
                $error->add('error', trans('errors.no_permission'));

                return redirect(route('media.show', ['media' => $media]))->with(['popup_message' => $error]);
            }
        }

        $articles = $media->articles;

        foreach ($articles as $article) {
            $article->media_id = null;
            $article->save();
        }

        //if it does not huave http means it's a local file
        if (!str_contains($media->url, 'http')){

            $path = str_replace('storage', 'public', $media->url);

            try {
                Storage::delete($path);
            } catch (Exception $e) {
                $errors = new MessageBag();
                $errors->add('error_deleting', trans('errors.deleting_file'));
                Session::flash('popup_errors', $errors);
            }

        }

        $media->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.media')]));


        return redirect()->route('media.index')->with('popup_message', $messages);
    }


    /**
     * Guarda uma imagem no disco, cria Media e retorna o url
     *
     * @param $file
     * @param $tags
     * @return string
     */
    public static function storeImage($file, $tags = null) {

        $originalName = str_slug($file->getClientOriginalName());
        $filename = str_random(3) . time() . str_random(6) . '_' . $originalName;
        $filename = self::removeLatin($filename);

        $url = '/storage/media/images/' . $filename;
        $path = '/public/media/images/';

        try {

            Storage::putFileAs(
                $path, $file, $filename
            );

            Media::create([
                'url' => $url,
                'media_type' => 'image',
                'tags' => $tags,
                'user_id' => Auth::user()->id,
                'visible' => true,
            ]);

        } catch (\Exception $e) {
            return null;
        }

        return $url;

    }


    /**
     *
     * Will save a squared image of any image ratio
     *
     * @param $image \Intervention\Image\Image
     * @param $filename string
     * @param $size int
     * @param $format string
     * @param $path string
     * @param $upzise boolean
     *
     * @return string
     */
    public static function storeSquareImage(\Intervention\Image\Image $image, $filename, $size = 500, $format = 'png', $path = null, $upzise = false) {

        if (!$path)
            $path = config('custom.media_images_folder');

        $filename = str_random(3) . time() . str_random(3) . '_' . $filename;
        $filename = str_replace(' ', '_', $filename);

        //Fit the image to a squared image
        if ($upzise)
            $image->fit($size, $size);
        else
            $image->fit($size, $size, function ($constraint) {
                $constraint->upsize();
            });

        //Save the image to the disk
        $image->save(public_path($path) . '/' .$filename . '.' . $format);

        $url = $path . '/' . $filename . '.' . $format;
        return $url;

    }

    public static function removeLatin($string) {

        $replaced = str_replace(
            ['ç', 'Ç', 'ã', 'Ã', 'õ', 'Õ', 'é', 'É', 'â', 'Â', 'ê', 'Ê', 'ó', 'Ó'],
            ['c', 'C', 'a', 'A', 'o', 'O', 'e', 'E', 'A', 'A', 'e', 'E', 'o', 'O'],
            $string
        );

        return $replaced;
    }
}
