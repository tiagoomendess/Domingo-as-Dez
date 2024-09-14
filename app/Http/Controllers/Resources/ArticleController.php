<?php

namespace App\Http\Controllers\Resources;

use App\Article;
use App\Audit;
use App\Media;
use Facebook\Facebook;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpKernel\Log\Logger;

class ArticleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:articles')->only(['index', 'show']);
        $this->middleware('permission:articles.edit')->only(['edit', 'update']);
        $this->middleware('permission:articles.create')->only(['create', 'store', 'destroy', 'postOnFacebook']);
    }

    /**
     * Display a listing of the resource.
     *
     *
     */
    public function index(Request $request)
    {
        if ($request->query->get('search')) {
            $articles = Article::search($request->query->all());
        } else {
            $articles = Article::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));
        }

        return view('backoffice.pages.articles', [
            'articles' => $articles,
            'searchFields' => Article::SEARCH_FIELDS,
            'queryParams' => $request->query->all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        return view('backoffice.pages.create_article');
    }

    /**
     * Store a newly created resource in storage.
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
        if (str_ends_with($title, '.') || str_ends_with($title, '!') || str_ends_with($title, '?')) {
            $title = substr($title, 0, -1);
        }

        $description = $request->input('description');
        $media_id = $request->input('selected_media_id');
        $text = $request->input('editor1');
        $date = $request->input('date');
        $tags = str_replace(', ', ',', $request->input('tags') ?? '');

        $media = Media::where('id', $media_id)->where('visible', true)->get();

        if (count($media) != 1)
            $media_id = null;

        if (empty($description))
            $description = substr(strip_tags($text), 0, 270) . '...';

        $article = Article::create([
            'title' => $title,
            'description' => $description,
            'media_id' => $media_id,
            'text' => $text,
            'date' => $date,
            'tags' => $tags,
            'user_id' => $user->id,
            'visible' => $visible,
            'facebook_post_id' => null,
        ]);

        Audit::add(
            Audit::ACTION_CREATE,
            'Article',
            null,
            $article->toArray()
        );

        return redirect(route('articles.show', ['article' => $article]));
    }

    /**
     * Display the specified resource.
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
     */
    public function edit($id)
    {
        $article = Article::findOrFail($id);

        return view('backoffice.pages.edit_article', ['article' => $article]);
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:155|string',
            'description' => 'nullable|max:280|string',
            'selected_media_id' => 'nullable|integer',
            'editor1' => 'required|max:65000|string',
            'tags' => 'nullable|string|max:280',
            'visible' => 'required',
        ]);

        $messages = new MessageBag();

        $article = Article::findOrFail($id);
        $old_article = $article->toArray();
        $user = Auth::user();
        if ($user->id != $article->user_id) {
            if(!$user->hasPermission('admin')) {

                $error = new MessageBag();
                $error->add('error', trans('errors.no_permission'));

                return redirect(route('articles.show', ['article' => $article]))->with(['popup_message' => $error]);
            }
        }

        $title = $request->input('title');
        if (str_ends_with($title, '.') || str_ends_with($title, '!') || str_ends_with($title, '?')) {
            $title = substr($title, 0, -1);
        }

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $media_id = $request->input('selected_media_id');
        $media = Media::where('id', $media_id)->where('visible', true)->get();

        if (count($media) != 1)
            $media_id = null;

        if (!$visible) {
            $article->title = $title;
        }

        $article->description = $request->input('description');
        $article->media_id = $media_id;
        $article->text = $request->input('editor1');
        $article->visible = $visible;
        $article->tags = str_replace(', ', ',', $request->input('tags'));

        if (empty($article->description))
            $article->description = substr(strip_tags($article->text), 0, 270) . '...';

        $article->save();

        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.article')]));

        Audit::add(
            Audit::ACTION_UPDATE,
            'Article',
            $old_article,
            $article->toArray()
        );

        return redirect(route('articles.show', ['article' => $article]))->with(['popup_message' => $messages]);
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $article = Article::findOrFail($id);
        $old_article = $article->toArray();

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

        Audit::add(Audit::ACTION_DELETE, 'Article', $old_article);

        return redirect(route('articles.index'))->with(['popup_message' => $message]);
    }

    public function postOnFacebook(Request $request, Article $article)
    {
        $messages = new MessageBag();

        if (!empty($article->facebook_post_id)) {
            $messages->add('error', 'Este artigo já foi publicado no facebook');
            return redirect(route('articles.index'))->with(['popup_message' => $messages]);
        }

        $request->validate([
            'message' => 'nullable|max:144|string',
        ]);

        $pageId = config('services.facebook.page_id');
        $appId = config('services.facebook.client_id');
        $appSecret = config('services.facebook.client_secret');
        $pageAccessToken = config('services.facebook.page_access_token');

        try {
            $fb = new Facebook([
                'app_id' => $appId,
                'app_secret' => $appSecret
            ]);

            $url = $article->getPublicUrl();
            //$url = str_replace('localhost:8000', 'domingoasdez.com', $url);

            $response = $fb->post("/$pageId/feed", [
                'message' => $request->input('message', ''),
                'link' => $url
            ], $pageAccessToken, null, 'v11.0');
        } catch (\Exception $e) {
            $messages->add('error', "Não foi possível publicar o artigo na página de facebook, a API retornou uma exceção: " . $e->getMessage());
            Log::info("Could not send post to facebook, error: " . $e->getMessage());

            return redirect(route('articles.index'))->with(['popup_message' => $messages]);
        }

        $decodedBody = $response->getDecodedBody();
        if ($response->getHttpStatusCode() == 200 && !empty($decodedBody) && isset($decodedBody['id'])) {
            $messages->add('success', "O Artigo foi publicado no facebook");
            $article->facebook_post_id = $decodedBody['id'];
            $article->save();
        } else {
            $messages->add('error', "Não foi possível publicar o artigo na página de facebook");
        }

        return redirect(route('articles.index'))->with(['popup_message' => $messages]);
    }
}
