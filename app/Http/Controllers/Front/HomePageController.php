<?php

namespace App\Http\Controllers\Front;

use App\Article;
use App\Competition;
use App\Game;
use App\Page;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HomePageController extends Controller
{
    //Landing page of the website
    public function index() {
        $cached_data = Cache::store('file')->get('homepage_data');
        if (!empty($cached_data)) {
            return view('front.pages.homepage', $cached_data);
        }

        Log::debug("Cache miss for homepage_data, generating new data");

        $articles = Article::where('visible', true)->orderBy('date', 'desc')->limit(3)->get();
        foreach ($articles as $article) {
            $article->public_url = $article->getPublicUrl();
        }

        if (count(Game::getLiveGames()) > 0)
            $live = true;
        else
            $live = false;

        $competitions = Competition::where('visible', true)->orderBy('id', 'asc')->limit(3)->get();
        foreach ($competitions as $competition) {
            $competition->name_slug = Str::slug($competition->name);
        }

        $total_players = DB::table('players')->count();
        $total_games = DB::table('games')->count();
        $total_goals = DB::table('goals')->count();
        $total_clubs = DB::table('clubs')->count();
        $pages = Page::where('visible', true)->orderBy('id', 'asc')->limit(10)->get();

        $view_data = [
            'articles' => $articles,
            'live' => $live,
            'competitions' => $competitions,
            'total_players' => $total_players,
            'total_games' => $total_games,
            'total_goals' => $total_goals,
            'total_clubs' => $total_clubs,
            'pages' => $pages,
        ];

        Cache::store('file')->put('homepage_data', $view_data, 60);

        return view('front.pages.homepage', $view_data);
    }

    public function home() {
        return redirect(route('homePage'));
    }
}
