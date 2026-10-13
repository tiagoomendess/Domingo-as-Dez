<?php

namespace App\Http\Controllers\Resources;

use App\Audit;
use App\Media;
use App\Services\ImageOptimizer;
use App\Services\UploadStorage;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

class MediaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:media.edit')->only(['edit', 'update']);
        $this->middleware('permission:articles.create')->only(['mediaQuery']);
        $this->middleware('permission:media.create')->only(['create', 'store', 'destroy']);
        $this->middleware('permission:media');
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request)
    {
        if ($request->query->get('search')) {
            $medias = Media::search($request->query->all());
        } else {
            $medias = Media::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));
        }

        return view('backoffice.pages.medias', [
            'medias' => $medias,
            'searchFields' => Media::SEARCH_FIELDS,
            'queryParams' => $request->query->all()
        ]);
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
            'url' => 'required_without:file|max:255|url|nullable|unique:media,url',
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

            if (Str::contains($url, ['youtube.com', 'youtu.be', 'youtube']))
                $mediaType = 'youtube';

            if (Str::contains($url, ['.jpeg', '.png', '.gif']))
                $mediaType = 'image';

            if (Str::contains($url, ['.mp4', '.avi', '.mpeg']))
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
                $extension = preg_replace('/[^a-z0-9]/', '', strtolower($file->getClientOriginalExtension())) ?: 'bin';

                $mediaType = 'other';
                $folder = 'files';

                if (Str::contains($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
                    $mediaType = 'image';
                    $folder = 'images';
                }

                if (Str::contains($extension, ['mp4', 'avi'])) {
                    $mediaType = 'video';
                    $folder = 'videos';
                }

                if ($mediaType === 'image') {
                    $url = static::storeUploadedImage($file);
                } else {
                    $url = UploadStorage::newUrl('/storage/media/' . $folder, $extension);
                    UploadStorage::putUploadedFile($url, $file);
                }

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

        if (!$media->generateThumbnail()) {
            $errors->add('thumbnail_error', 'Erro ao gerar a thumbnail');
            return redirect()->back()->withErrors($errors);
        }

        $message = new MessageBag();
        $message->add('success', trans('success.model_added', ['model_name' => trans('models.media')]));

        Audit::add(Audit::ACTION_CREATE, "Media", null, $media->toArray());

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
        if (!Str::contains($media->url, 'http')){

            UploadStorage::delete($media->url);
            UploadStorage::delete($media->thumbnail_url);
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

        try {

            $url = static::storeUploadedImage($file);

            $media = Media::create([
                'url' => $url,
                'media_type' => 'image',
                'tags' => $tags,
                'user_id' => Auth::user()->id,
                'visible' => true,
            ]);

            try {
                $media->generateThumbnail();
            } catch (\Exception $e) {
                // Thumbnail is optional; the original image was already stored.
            }

        } catch (\Exception $e) {
            return null;
        }

        return $url;

    }


    /**
     * Saves an uploaded image under a new name: rotated, capped to ImageOptimizer::CONTENT_MAX_SIZE and
     * re-encoded (PNG only when transparent). GIF and SVG are stored as uploaded.
     */
    private static function storeUploadedImage(UploadedFile $file): string
    {
        $folder = config('custom.media_images_folder');
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ImageOptimizer::KEEP_ORIGINAL)) {
            $url = UploadStorage::newUrl($folder, $extension);
            UploadStorage::putUploadedFile($url, $file);

            return $url;
        }

        $image = ImageOptimizer::orientate(Image::make($file));

        return ImageOptimizer::store(ImageOptimizer::limitSize($image), $folder);
    }

    /**
     * Saves a square crop of any image (photos, emblems) under a new name.
     *
     * @param  string|null  $format  'jpg' for photos; null keeps PNG when the image is transparent (emblems)
     * @param  string|null  $folder  /storage/... folder, media images by default
     * @return string the new URL
     */
    public static function storeSquareImage(\Intervention\Image\Image $image, int $size = ImageOptimizer::SQUARE_SIZE, ?string $format = 'jpg', ?string $folder = null) {

        ImageOptimizer::orientate($image);

        $image->fit($size, $size, function ($constraint) {
            $constraint->upsize();
        });

        return ImageOptimizer::store($image, $folder ?: config('custom.media_images_folder'), $format);

    }
}
