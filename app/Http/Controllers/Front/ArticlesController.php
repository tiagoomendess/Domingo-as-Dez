<?php

namespace App\Http\Controllers\Front;

use App\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ArticlesController extends Controller
{
    public function index() {

        $articles = Article::where('visible', true)->orderBy('date', 'desc')->paginate(config('custom.results_per_page'));

        return view('front.pages.articles', ['articles' => $articles]);
    }

    public function show($year, $month, $day, $slug) {

        if($year < 1070)
            return abort(404);
        if($month < 1 || $month > 12)
            return abort(404);
        if ($day < 1 || $day > 31)
            return abort(404);

        $carbon = Carbon::create($year, $month, $day);

        if (!$carbon)
            return abort(404);


        $articles = DB::table('articles')->where('date', $carbon->format("Y-m-d 0:0:0"))->get();

        if ($articles->count() == 0)
            return abort(404);

        $found_article = null;

        foreach ($articles as $article) {

            if (str_slug($article->title) == $slug) {
                $found_article = $article;
                break;
            }

        }

        if (!$found_article)
            return abort(404);

        return view('front.pages.article', ['article' => Article::find($found_article->id), 'navbar_title' => trans('front.news_singular')]);


    }
}
