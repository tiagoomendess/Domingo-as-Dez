<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resources\MediaController;
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
            'upload' => 'image|max:20000',
        ]);

        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $url = MediaController::storeImage($request->file('upload'), $originName);
            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $msg = 'Imagem enviada com sucesso';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            return response($response, 200, ['Content-type' => 'text/html; charset=utf-8']);
        }

        return response('Não foi encontrado nenhum ficheiro para carregar', 400);
    }
}
