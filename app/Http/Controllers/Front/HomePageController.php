<?php

namespace App\Http\Controllers\Front;

use App\Article;
use App\Competition;
use App\Game;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomePageController extends Controller
{
    //Landing page of the website
    public function index() {

        $user = Auth::user();

        $articles = Article::where('visible', true)->orderBy('date', 'desc')->limit(3)->get();

        if (count(Game::getLiveGames()) > 0)
            $live = true;
        else
            $live = false;

        $competitions = Competition::where('visible', true)->orderBy('id', 'asc')->limit(3)->get();

        return view('front.pages.homepage', [
            'articles' => $articles,
            'live' => $live,
            'competitions' => $competitions,
        ]);
    }

    public function home() {
        return redirect(route('homePage'));
    }
}
