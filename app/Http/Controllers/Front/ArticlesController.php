<?php

namespace App\Http\Controllers\Front;

use App\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticlesController extends Controller
{
    public function index() {

        $articles = Article::where('visible', true)->orderBy('date', 'desc')->paginate(config('custom.results_per_page'));

        return view('front.pages.articles', ['articles' => $articles]);
    }

    public function show() {
    }
}
