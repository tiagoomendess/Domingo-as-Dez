<?php

namespace App\Http\Controllers\Front;

use App\Audit;
use App\Competition;
use App\Game;
use App\MvpVotes;
use App\Player;
use App\ScoreReport;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GamesController extends Controller
{

    public function show($competition_slug, $season_slug, $group_slug, $round, $clubs_slug) {

        $competition = Competition::getCompetitionBySlug($competition_slug);

        if (!$competition)
            return abort(404);

        $years = mb_split('-', $season_slug, 2);

        if (count($years) == 2)
            $season = $competition->getSeasonByYears($years[0], $years[1]);
        else if (count($years) == 1)
            $season = $competition->getSeasonByYears($years[0], $years[0]);
        else
            return abort(404);

        if (!$season)
            return abort(404);

        $group = $season->getGroupBySlug($group_slug);

        if (!$group)
            return abort(404);

        $clubs = mb_split('-vs-', $clubs_slug, 2);

        if (count($clubs) != 2)
            return abort(404);

        /** @var Game $game */
        $game = $group->getGameByClubNameSlug($round, $clubs[0], $clubs[1]);

        if (!$game || !$game->visible)
            return abort(404);

        $mvp = DB::table('mvp_votes')
            ->select(DB::raw('player_id, count(player_id) as amount'))
            ->where('game_id', $game->id)
            ->groupBy('player_id')
            ->orderBy('amount', 'desc')
            ->first();

        if (!empty($mvp))
            $mvp->player = Player::find($mvp->player_id);

        /** @var User $user */
        $user = Auth::user();
        if ($user) {
            $mvpVote = MvpVotes::where('user_id', $user->id)->where('game_id', $game->id)->first();
        } else {
            $mvpVote = null;
        }

        // get past games
        $past_games = Game::where('visible', true)
            ->where('finished', true)
            ->where('date', '<', $game->date)
            ->whereRaw(
                "((home_team_id = ? and away_team_id = ?) or (home_team_id = ? and away_team_id = ?))",
                [ $game->home_team_id, $game->away_team_id, $game->away_team_id, $game->home_team_id]
            )
            ->orderByDesc('date')
            ->get();

        if (!$game->started() && !$game->finished) {
            $home_team_last_games = Game::where('visible', true)
                ->where('finished', true)
                ->where('date', '<', $game->date)
                ->whereRaw("(home_team_id = ? or away_team_id = ?)", [$game->home_team_id, $game->home_team_id])
                ->orderByDesc('date')
                ->limit(4)
                ->get();

            $away_team_last_games = Game::where('visible', true)
                ->where('finished', true)
                ->where('date', '<', $game->date)
                ->whereRaw("(home_team_id = ? or away_team_id = ?)", [$game->away_team_id, $game->away_team_id])
                ->orderByDesc('date')
                ->limit(4)
                ->get();
        } else {
            $home_team_last_games = [];
            $away_team_last_games = [];
        }

        return view(
            'front.pages.game',
            [
                'game' => $game,
                'mvp' => $mvp,
                'mvp_vote' => $mvpVote,
                'past_games' => $past_games,
                'home_team_last_games' => $home_team_last_games,
                'away_team_last_games' => $away_team_last_games,
            ]
        );
    }

    public function liveMatches() {
        return view('front.pages.live_matches');
    }

    public function today() {

        $now = Carbon::now();
        $begin = clone($now)->startOfDay();
        $end = clone($now)->endOfDay();

        $games = Game::where('date', '>', $begin)
            ->where('date', '<', $end)
            ->where('visible', true)
            ->orderBy('date', 'asc')
            ->get();

        $closest = Game::where('date', '>', $end)
            ->where('visible', true)
            ->orderBy('date', 'asc')
            ->first();

        return view('front.pages.today', [
            'games' => $games,
            'closest' => $closest
        ]);
    }

    public function todayEdit()
    {
        if (!has_permission('games.edit'))
            return abort(404);

        $now = Carbon::now();
        $begin = clone($now)->startOfDay();
        $end = clone($now)->endOfDay();

        $games = Game::where('date', '>', $begin)
            ->where('date', '<', $end)
            ->where('visible', true)
            ->orderBy('date', 'asc')
            ->get();

        $gameIds = $games->pluck('id')->toArray();
        $all_reports = DB::table('score_reports')->whereIn('game_id', $gameIds)
            ->orderBy('id', 'desc')
            ->limit(200)
            ->get();

        $reports_by_game = [];
        // Group by game_id
        foreach ($all_reports as $report) {
            if (!isset($reports_by_game[$report->game_id]))
                $reports_by_game[$report->game_id] = [];

            $reports_by_game[$report->game_id][] = $report;
        }

        $score_reports = [];
        foreach ($reports_by_game as $game_id => $reports) {
            foreach ($reports as $report) {
                $key = $report->home_score . '-' . $report->away_score;

                if (!isset($score_reports[$game_id][$key]))
                    $score_reports[$game_id][$key] = 0;

                if (count($score_reports[$game_id]) > 3)
                    break;

                $score_reports[$game_id][$key]++;
            }
        }

        return view('front.pages.today_edit', [
            'games' => $games,
            'score_reports' => $score_reports
        ]);
    }

    public function todayUpdateScore(Request $request) {

        if (!has_permission('games.edit'))
            return abort(404);

        $request->validate([
            'game_id' => 'required|min:1',
            'goals_home' => 'required|min:0',
            'goals_away' => 'required|min:0',
            'finished' => 'integer|min:0|max:1'
        ]);

        $game = Game::findOrFail($request->input('game_id'));
        $old_game = $game->toArray();
        $game->goals_home = $request->input('goals_home');
        $game->goals_away = $request->input('goals_away');
        $game->finished = (bool)$request->input('finished', false);
        $game->save();

        // invalidate cache for live games because score was updated
        Cache::store('file')->forget('live_matches');

        Audit::add(Audit::ACTION_UPDATE, 'Game', $old_game, $game->toArray());
        //$this->createScoreReport($game); -> Removed for now, maybe will add later

        return redirect()->route('games.today_edit');
    }

    private function createScoreReport(Game $game) {
        try {
            $user = Auth::user();
            ScoreReport::create([
                'user_id' => $user ? $user->id : null,
                'game_id' => $game->id,
                'home_score' => $game->getHomeScore(),
                'away_score' => $game->getAwayScore(),
                'source' => 'today_edit',
                'ip_address' => Str::limit(request()->getClientIp(), 255, ''),
                'user_agent' => Str::limit(request()->userAgent(), 255, ''),
                'uuid' => $this->guidv4(),
            ]);
        } catch (\Exception $e) {
            Log::error("Error creating score report on today edit: " . $e->getMessage());
        }
    }

    private function guidv4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
