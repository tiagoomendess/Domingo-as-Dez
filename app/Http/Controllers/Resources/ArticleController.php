<?php

namespace App\Http\Controllers\Resources;

use App\Article;
use App\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            'selected_media_id' => 'nullable|integer',
            'editor1' => 'required|max:65000|string',
            'date' => 'required|date',
            'tags' => 'nullable|string|max:280',
            'visible' => 'required',
        ]);

        $user = Auth::user();

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $title = $request->input('title');
        $description = $request->input('description');
        $media_id = $request->input('selected_media_id');
        $text = $request->input('editor1');
        $date = $request->input('date');
        $tags = str_replace(', ', ',', $request->input('tags'));

        $date = Carbon::createFromFormat('d-m-Y', $date)->toDateTimeString();

        $article = Article::create([
            'title' => $title,
            'description' => $description,
            'media_id' => $media_id,
            'text' => $text,
            'date' => $date,
            'tags' => $tags,
            'user_id' => $user->id,
        ]);

        return redirect(route('articles.show', ['article' => $article]));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = Article::findOrFail($id);

        if ($article->media_id == null)
            $media = null;
        else
            $media = Media::find($article->media_id);

        return view('backoffice.pages.article', ['article' => $article, 'media' => $media]);
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
