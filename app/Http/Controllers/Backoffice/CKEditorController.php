<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resources\MediaController;
use App\Media;
use http\Env\Response;
use Illuminate\Http\Request;

class CKEditorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:media');
    }

    /**
     * @param Request $request
     */
    public function upload(Request $request)
    {
        $request->validate([
            'upload' => 'file|mimes:jpeg,jpg,png|max:20000',
        ]);

        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $existingPhoto = Media::where('tags', 'ckeditor-upload,' . $originName)->first();

            // Only upload new file if it does not exist already
            if ($existingPhoto) {
                $url = $existingPhoto->url;
            } else {
                $url = MediaController::storeImage($request->file('upload'), 'ckeditor-upload,' . $originName);
            }

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $msg = 'Imagem enviada com sucesso';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            return response($response, 200, ['Content-type' => 'text/html; charset=utf-8']);
        }

        return response('Não foi encontrado nenhum ficheiro para carregar', 400);
    }
}
