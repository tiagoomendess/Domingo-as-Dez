<?php

namespace App\Http\Controllers\Front;

use App\Competition;
use App\Http\Controllers\Controller;
use App\Player;
use App\Season;
use App\Team;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CompetitionStatsController extends Controller
{
    public function __construct()
    {

    }

    public function show(string $competition_slug, string $season_slug): View
    {
        $competition = Competition::getCompetitionBySlug($competition_slug);
        if (!$competition || !$competition->visible)
            abort(404);

        $season_years = explode("-", $season_slug, 2);
        $season = $competition->getSeasonByYears($season_years[0], isset($season_years[1]) ? $season_years[1] : null);
        if (!$season || !$season->visible)
            abort(404);

        $bestScorers = self::getBestScorers($season);
        $attack = self::getBestAndWorstAttack($season);
        $defense = self::getBestAndWorstDefense($season);

        return view("front.pages.competition_stats", [
            'competition' => $competition,
            'bestScorers' => $bestScorers,
            'attack' => $attack,
            'defense' => $defense
        ]);
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

        $goalCount = $goals->groupBy('team_id');

        $goalCount = $goalCount->sortByDesc(function ($a) {
            return count($a);
        });

        $finalList = collect();
        foreach ($goalCount as $key => $value) {
            $finalList->push([
                'team_id' => $key,
                'goal_count' => count($value)
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
        $games = collect();
        foreach ($game_groups as $game_group) {

            $games = $games->concat($game_group->games);

            foreach ($games as $game) {

                $homeGoals = $game->getTotalHomeGoals();
                $awayGoals = $game->getTotalAwayGoals();

                $data->has($game->home_team_id) ? $data->put($game->home_team_id, $data->get($game->home_team_id) + $awayGoals) : $data->put($game->home_team_id, $awayGoals);
                $data->has($game->away_team_id) ? $data->put($game->away_team_id, $data->get($game->away_team_id) + $homeGoals) : $data->put($game->away_team_id, $homeGoals);
            }
        }

        $data = $data->sortBy(function($a) {
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
