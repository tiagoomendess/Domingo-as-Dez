<?php

namespace App\Http\Controllers\Front;

use App\Media;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PublicMediaController extends Controller
{
    public function show($filename) {

        $media = Media::where('url', '/media/' . $filename)->where('visible', true)->get();

        if ($media->count() != 1)
            abort(404);

        $file = Storage::get('/media/' . $filename);

        if (!$file)
            abort(404);

        return new Image($file);

    }
}
