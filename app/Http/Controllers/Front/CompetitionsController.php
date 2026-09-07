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

    public function show($season_slug, $competition_slug)
    {
        if (Season::isLegacyFullYearSlug($season_slug)) {
            $years = Season::parseNameSlug($season_slug);
            if (!$years) {
                abort(404);
            }
            $canonicalSeason = (new Season([
                'start_year' => $years[0],
                'end_year' => $years[1],
            ]))->getNameSlug();

            return redirect()->route('competition', [
                'season_slug' => $canonicalSeason,
                'competition_slug' => $competition_slug,
            ], 301);
        }

        $season = Season::findBySeasonAndCompetitionSlug($season_slug, $competition_slug);

        if (!$season) {
            abort(404);
        }

        $competition = $season->competition;

        // Canonicalize display slug if URL used the old competition name
        if ($season->getDisplaySlug() !== $competition_slug) {
            return redirect()->route('competition', [
                'season_slug' => $season->getNameSlug(),
                'competition_slug' => $season->getDisplaySlug(),
            ], 301);
        }

        if ($season->getNameSlug() !== $season_slug) {
            return redirect()->route('competition', [
                'season_slug' => $season->getNameSlug(),
                'competition_slug' => $season->getDisplaySlug(),
            ], 301);
        }

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
            'season' => $season,
            'season_slug' => $season->getNameSlug(),
            'display_name' => $season->getDisplayName(),
            'display_picture' => $season->getDisplayPicture(),
            'display_slug' => $season->getDisplaySlug(),
            'game_started_and_not_finished' => $gameStartedAndNotFinished,
        ]);
    }

    /**
     * 301 from /competicoes/{slug} to the latest season URL for that display slug.
     */
    public function redirectLegacySlug($slug)
    {
        $result = Competition::findLatestByDisplaySlug($slug);

        if (!$result) {
            abort(404);
        }

        return redirect($result['season']->getPublicUrl(), 301);
    }

    public function showAll()
    {
        $competitions = Competition::where('visible', true)->orderedForFrontend()->get();

        foreach ($competitions as $competition) {
            $season = $competition->getLatestVisibleSeason();
            $competition->display_name = $season ? $season->getDisplayName() : $competition->name;
            $competition->display_picture = $season ? $season->getDisplayPicture() : $competition->picture;
            $competition->public_url = $competition->getPublicUrl($season);
        }

        return view('front.pages.competitions', ['competitions' => $competitions]);
    }
}
