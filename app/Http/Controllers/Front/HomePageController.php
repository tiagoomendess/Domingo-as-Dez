<?php

namespace App\Http\Controllers\Front;

use App\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomePageController extends Controller
{
    //Landing page of the website
    public function index() {

        $user = Auth::user();

        $articles = Article::where('visible', true)->orderBy('date', 'desc')->limit(6)->get();

        return view('front.pages.homepage', [
            'articles' => $articles,
        ]);
    }

    public function home() {
        return redirect(route('homePage'));
    }
}
