<?php

namespace App\Http\Controllers\Front;

use App\Article;
use App\Competition;
use App\Game;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $total_players = DB::table('players')->count();
        $total_games = DB::table('games')->count();
        $total_goals = DB::table('goals')->count();
        $total_clubs = DB::table('clubs')->count();

        return view('front.pages.homepage', [
            'articles' => $articles,
            'live' => $live,
            'competitions' => $competitions,
            'total_players' => $total_players,
            'total_games' => $total_games,
            'total_goals' => $total_goals,
            'total_clubs' => $total_clubs,
        ]);
    }

    public function home() {
        return redirect(route('homePage'));
    }
}
