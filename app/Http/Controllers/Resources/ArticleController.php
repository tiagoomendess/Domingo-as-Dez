<?php

namespace App\Http\Controllers\Resources;

use App\Article;
use App\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:articles');
        $this->middleware('permission:articles.edit')->except('index');
    }

    /**
     * Display a listing of the resource.
     *
     *
     */
    public function index()
    {
        $articles = Article::paginate(config('custom.results_per_page'));
        return view('backoffice.pages.articles')->with(['articles' => $articles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_article');
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
            'title' => 'required|max:155|string',
            'description' => 'required|max:280|string',
            'editor1' => 'required|max:65000|string',
            'url' => 'required_without:file|max:255|url|nullable',
            'file' => 'nullable|required_without:url|file|max:200000',
            'visible' => 'required',
        ]);

        $user = Auth::user();

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;


        if ($request->input('url') != null) {

            $media = Media::where('url', $request->input('url'))->first();

            if (!$media) {

                $media = Media::create([
                    ''
                ]);
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = Article::findOrFail($id);

        return view('backoffice.pages.article', ['article' => $article]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $article = Article::findOrFail($id);

        return view('backoffice.pages.edit_article', ['article' => $article]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
