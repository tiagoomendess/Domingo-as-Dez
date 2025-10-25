<?php

namespace App\Http\Controllers\Front;

use App\Article;
use App\ArticleComment;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ArticlesController extends Controller
{
    public function index()
    {

        $articles = Article::where('visible', true)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(8);

        return view('front.pages.articles', ['articles' => $articles]);
    }

    public function show($year, $month, $day, $slug)
    {
        if ($year < 1970)
            return abort(404);
        if ($month < 1 || $month > 12)
            return abort(404);
        if ($day < 1 || $day > 31)
            return abort(404);

        $carbon = Carbon::create($year, $month, $day);

        if (!$carbon)
            return abort(404);

        $startOfDay = $carbon->copy()->startOfDay();
        $endOfDay = $carbon->copy()->endOfDay();

        $articles = Article::whereBetween('date', [$startOfDay, $endOfDay])->get();

        if ($articles->count() == 0)
            return abort(404);

        $found_article = null;

        foreach ($articles as $article) {

            if (Str::slug($article->title) == $slug) {
                $found_article = $article;
                break;
            }

        }

        if (!$found_article)
            return abort(404);

        $article = $found_article;

        if (!$article->visible && !has_permission('articles'))
            return abort(404);

        $img = Image::make(public_path($article->getThumbnail()));

        $comments = ArticleComment::where([
            'deleted' => false,
            'article_id' => $article->id,
            'article_comment_id' => null
        ])->get();

        return view('front.pages.article', [
            'article' => $article,
            'navbar_title' => trans('front.news_singular'),
            'img_width' => $img->width(),
            'img_height' => $img->height(),
            'comments' => $comments,
            'user' => Auth::user()
        ]);
    }
}
