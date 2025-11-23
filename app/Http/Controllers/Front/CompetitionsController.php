<?php

namespace App\Http\Controllers\Front;

use App\Competition;
use App\Game;
use App\Season;
use App\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Cache;

class CompetitionsController extends Controller
{

    public function show($slug)
    {
        $competition = Competition::getCompetitionBySlug($slug);

        if (!$competition || !$competition->visible)
            abort(404);

        $mostRecentSeason = $competition->seasons()->where('visible', true)->orderByDesc('id')->limit(1)->first();
        $seasonSlug = $mostRecentSeason->start_year . '-' . $mostRecentSeason->end_year;

        $gameStartedAndNotFinished = false;
        $cacheKey = "competition_game_started_and_not_finished_cache_" . $competition->id;
        $cachedData = Cache::store('file')->get($cacheKey);
        if (!empty($cachedData)) {
            $gameStartedAndNotFinished = $cachedData;
        } else {
            $allLiveGames = Game::getLiveGames();
            foreach ($allLiveGames as $game) {
                if ($game->started() && !$game->finished) {
                    Cache::store('file')->put($cacheKey, true, 60);
                    $gameStartedAndNotFinished = true;
                    break;
                }
            }
        }

        return view('front.pages.competition', [
            'competition' => $competition,
            'season_slug' => $seasonSlug,
            'game_started_and_not_finished' => $gameStartedAndNotFinished,
        ]);
    }

    public function showAll()
    {

        $competitions = Competition::where('visible', true)->get();

        return view('front.pages.competitions', ['competitions' => $competitions]);
    }
}
