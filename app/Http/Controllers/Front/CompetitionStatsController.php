<?php

namespace App\Http\Controllers\Front;

use App\Competition;
use App\Http\Controllers\Controller;
use App\Player;
use App\Season;
use App\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CompetitionStatsController extends Controller
{
    public function __construct()
    {

    }

    public function show(string $season_slug, string $competition_slug)
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

            return redirect()->route('competition.stats', [
                'season_slug' => $canonicalSeason,
                'competition_slug' => $competition_slug,
            ], 301);
        }

        $season = Season::findBySeasonAndCompetitionSlug($season_slug, $competition_slug);
        if (!$season) {
            abort(404);
        }

        $competition = $season->competition;

        if ($season->getDisplaySlug() !== $competition_slug || $season->getNameSlug() !== $season_slug) {
            return redirect()->route('competition.stats', [
                'season_slug' => $season->getNameSlug(),
                'competition_slug' => $season->getDisplaySlug(),
            ], 301);
        }

        $bestScorers = self::getBestScorers($season);
        $attack = self::getBestAndWorstAttack($season);
        $defense = self::getBestAndWorstDefense($season);

        return view("front.pages.competition_stats", [
            'competition' => $competition,
            'season' => $season,
            'display_name' => $season->getDisplayName(),
            'display_picture' => $season->getDisplayPicture(),
            'bestScorers' => $bestScorers,
            'attack' => $attack,
            'defense' => $defense
        ]);
    }

    /**
     * 301 from old /competicoes/{competition}/{season}/estatisticas to season-first URL.
     */
    public function redirectLegacy(string $competition_slug, string $season_slug)
    {
        $years = Season::parseNameSlug($season_slug);
        if (!$years) {
            abort(404);
        }

        $canonicalSeason = (new Season([
            'start_year' => $years[0],
            'end_year' => $years[1],
        ]))->getNameSlug();

        $season = Season::findBySeasonAndCompetitionSlug($canonicalSeason, $competition_slug);
        if (!$season) {
            // Try with the raw parsed years against canonical competition slug
            $competition = Competition::getCompetitionBySlug($competition_slug);
            if (!$competition) {
                abort(404);
            }
            $season = $competition->getSeasonByYears($years[0], $years[1]);
            if (!$season || !$season->visible) {
                abort(404);
            }
        }

        return redirect()->route('competition.stats', [
            'season_slug' => $season->getNameSlug(),
            'competition_slug' => $season->getDisplaySlug(),
        ], 301);
    }

    public static function getBestScorers(Season $season, int $limit = 10): Collection
    {
        $goals = self::getAllSeasonGoals($season);

        /** Remove own goals */
        $goals = $goals->filter(function ($item) {
            return $item->own_goal != 1;
        });

        /** @var Collection */
        $bestScorers = $goals->groupBy('player_id');

        $bestScorers = $bestScorers->sortByDesc(function ($a) {
            return count($a);
        });

        $bestScorers = $bestScorers->forget('');
        $topScorers = collect();
        foreach ($bestScorers->take($limit) as $key => $value) {
            $topScorers->push([
                'amount' => count($value),
                'player' => Player::find($key)
            ]);
        }

        return $topScorers;
    }

    private static function getBestAndWorstAttack(Season $season): array
    {
        $goals = self::getAllSeasonGoals($season);

        $a = [];
        $allTeamIds = [];
        foreach ($season->game_groups as $game_group) {
            foreach ($game_group->games as $game) {
                if (!isset($a[$game->home_team->id])) {
                    $a[$game->home_team->id] = true;
                    $allTeamIds[] = $game->home_team->id;
                }

                if (!isset($a[$game->away_team->id])) {
                    $a[$game->away_team->id] = true;
                    $allTeamIds[] = $game->away_team->id;
                }

            }
        }

        $goalCount = $goals->groupBy('team_id');

        $goalCount = $goalCount->sortByDesc(function ($a) {
            return count($a);
        });

        $preList = [];
        foreach ($goalCount as $key => $value) {
            $preList[$key] = count($value);
        }

        foreach ($allTeamIds as $teamId) {
            if (empty($preList[$teamId]))
                $preList[$teamId] = 0;
        }

        $finalList = collect();
        foreach ($preList as $key => $value) {
            $finalList->push([
                'team_id' => $key,
                'goal_count' => $value
            ]);
        }

        $best = $finalList->first();
        $worst = $finalList->last();
        $best['team'] = Team::find($best['team_id']);
        $worst['team'] = Team::find($worst['team_id']);

        return [
            'best' => $best,
            'worst' => $worst
        ];
    }

    private static function getBestAndWorstDefense(Season $season): array
    {
        $game_groups = $season->game_groups;

        $data = collect();
        foreach ($game_groups as $game_group) {
            foreach ($game_group->games as $game) {

                $homeGoals = $game->getTotalHomeGoals();
                $awayGoals = $game->getTotalAwayGoals();

                $data->has($game->home_team_id) ? $data->put($game->home_team_id, $data->get($game->home_team_id) + $awayGoals) : $data->put($game->home_team_id, $awayGoals);
                $data->has($game->away_team_id) ? $data->put($game->away_team_id, $data->get($game->away_team_id) + $homeGoals) : $data->put($game->away_team_id, $homeGoals);
            }
        }

        $data = $data->sortBy(function ($a) {
            return $a;
        });

        $finalData = collect();
        foreach ($data as $key => $value) {
            $finalData->push([
                'team_id' => $key,
                'goal_count' => $value
            ]);
        }

        $best = $finalData->first();
        $worst = $finalData->last();
        $best['team'] = Team::find($best['team_id']);
        $worst['team'] = Team::find($worst['team_id']);

        return [
            'best' => $best,
            'worst' => $worst
        ];
    }

    private static function getAllSeasonGoals(Season $season): Collection
    {
        $game_groups = $season->game_groups;

        $games = collect();
        foreach ($game_groups as $game_group) {
            $games = $games->concat($game_group->games);
        }

        $goals = collect();
        foreach ($games as $game) {
            $goals = $goals->concat($game->goals);
        }

        return $goals;
    }
}
