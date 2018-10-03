<?php

namespace App\Http\Controllers\Resources;

use App\Article;
use App\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;

class ArticleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:articles')->only(['index', 'show']);
        $this->middleware('permission:articles.edit')->only(['edit', 'update']);
        $this->middleware('permission:articles.create')->only(['create', 'store', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     *
     */
    public function index()
    {
        $articles = Article::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));
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
            'description' => 'nullable|max:280|string',
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

        $media = Media::where('id', $media_id)->where('visible', true)->get();

        if (count($media) != 1)
            $media_id = null;

        $article = Article::create([
            'title' => $title,
            'description' => $description,
            'media_id' => $media_id,
            'text' => $text,
            'date' => $date,
            'tags' => $tags,
            'user_id' => $user->id,
            'visible' => $visible,
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
        $request->validate([
            'title' => 'required|max:155|string',
            'description' => 'nullable|max:280|string',
            'selected_media_id' => 'nullable|integer',
            'editor1' => 'required|max:65000|string',
            'date' => 'required|date',
            'tags' => 'nullable|string|max:280',
            'visible' => 'required',
        ]);

        $messages = new MessageBag();

        $article = Article::findOrFail($id);
        $user = Auth::user();
        if ($user->id != $article->user_id) {
            if(!$user->hasPermission('admin')) {

                $error = new MessageBag();
                $error->add('error', trans('errors.no_permission'));

                return redirect(route('articles.show', ['article' => $article]))->with(['popup_message' => $error]);
            }
        }

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $media_id = $request->input('selected_media_id');
        $media = Media::where('id', $media_id)->where('visible', true)->get();

        if (count($media) != 1)
            $media_id = null;

        $article->title = $request->input('title');
        $article->description = $request->input('description');
        $article->media_id = $media_id;
        $article->text = $request->input('editor1');
        $article->date = $request->input('date');
        $article->visible = $visible;
        $article->tags = str_replace(', ', ',', $request->input('tags'));
        $article->save();

        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.article')]));

        return redirect(route('articles.show', ['article' => $article]))->with(['popup_message' => $messages]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $article = Article::findOrFail($id);

        if ($user->id != $article->user_id) {
            if(!$user->hasPermission('admin')) {

                $error = new MessageBag();
                $error->add('error', trans('errors.no_permission'));

                return redirect(route('articles.show', ['article' => $article]))->with(['popup_message' => $error]);
            }
        }

        $article->delete();
        $message = new MessageBag();
        $message->add('success', trans('success.model_deleted', ['model_name' => trans('models.article')]));

        return redirect(route('articles.index'))->with(['popup_message' => $message]);
    }
}
