<?php

namespace App\Http\Controllers\Front;

use App\Audit;
use App\Competition;
use App\Game;
use App\MvpVotes;
use App\Partner;
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
    public function __construct() {
        $this->middleware('ensure-uuid')->only(['show', 'liveMatches', 'today']);
        $this->middleware('permission:score_update')->only(['todayEdit', 'todayUpdateScore', 'listScoreReports']);
        $this->middleware('permission:admin')->only(['updateIsFake']);
    }

    public function index(Request $request) {
        $cacheKey = "all-games-page-" . $request->query('page', 1);
        $cached_data = Cache::store('file')->get($cacheKey);
        if (!empty($cached_data))
            return view('front.pages.games', $cached_data);

        Log::debug("Cache miss for $cacheKey, generating new one");

        $games = Game::where('visible', true)
            ->orderBy('date', 'desc')
            ->paginate(10);

        foreach ($games as $game) {
            $game->public_url = $game->getPublicUrl();
            $game->started = $game->started();
            $game->home_team_three_letters = $game->home_team->club->getThreeLetterName();
            $game->away_team_three_letters = $game->away_team->club->getThreeLetterName();
            $game->home_team_emblem = $game->home_team->club->getEmblem();
            $game->away_team_emblem = $game->away_team->club->getEmblem();
            $game->home_score = $game->getHomeScore();
            $game->away_score = $game->getAwayScore();
        }

        $competitions = Competition::where('visible', true)
            ->orderBy('id', 'asc')->get();

        $data = [
            'games' => $games,
            'competitions' => $competitions
        ];
        Cache::store('file')->add($cacheKey, $data, 240);

        return view('front.pages.games', $data);
    }

    public function show($competition_slug, $season_slug, $group_slug, $round, $clubs_slug) {
        $cache_key = "game-cache-$competition_slug-$season_slug-$group_slug-$round-$clubs_slug";
        $cached_data = Cache::get($cache_key);
        if (!empty($cached_data)) {
            // flash interview is not cached
            $flash_interview_link = $this->getFlashInterviewLink($cached_data['game']);

            $cached_data['flash_interview_link'] = $flash_interview_link;
            return view('front.pages.game', $cached_data);
        }

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

        // Build stats for direct confrontations
        $past_result_stats = [
            'home_win_total' => 0,
            'home_win_percent' => 0,
            'draw_total' => 0,
            'draw_percent' => 0,
            'away_win_total' => 0,
            'away_win_percent' => 0,
            'total_games' => count($past_games),
        ];

        /**
         * @var Game $past_game
         */
        foreach ($past_games as $past_game) {
            if ($past_game->isDraw()) {
                $past_result_stats['draw_total']++;
                continue;
            }

            if ($past_game->winner()->id == $game->home_team_id) {
                $past_result_stats['home_win_total']++;
            } else {
                $past_result_stats['away_win_total']++;
            }
        }

        if ($past_result_stats['total_games'] > 0) {
            $past_result_stats['home_win_percent'] = $past_result_stats['home_win_total'] / $past_result_stats['total_games'] * 100;
            $past_result_stats['away_win_percent'] = $past_result_stats['away_win_total'] / $past_result_stats['total_games'] * 100;
            $past_result_stats['draw_percent'] = $past_result_stats['draw_total'] / $past_result_stats['total_games'] * 100;
        }

        $view_data = [
            'game' => $game,
            'mvp' => $mvp,
            'mvp_vote' => $mvpVote,
            'past_games' => $past_games,
            'home_team_last_games' => $home_team_last_games,
            'away_team_last_games' => $away_team_last_games,
            'past_result_stats' => $past_result_stats
        ];

        Cache::store('file')->add($cache_key, $view_data, 60);

        // flash interview link
        $view_data['flash_interview_link'] = $this->getFlashInterviewLink($game);

        return view(
            'front.pages.game',
            $view_data
        );
    }

    /**
     * @return string|null
     */
    private function getFlashInterviewLink(Game $game) {
        $user = Auth::user();
        if (empty($user)) {
            return null;
        }

        $gameComment = null;
        // Check if home team email is same as user email
        if ($user->email == $game->getHomeTeamContactEmail()) {
            $gameComment = $game->gameComments()->where('team_id', $game->home_team_id)->first();
        }

        // Check if away team email is same as user email
        if ($user->email == $game->getAwayTeamContactEmail()) {
            $gameComment = $game->gameComments()->where('team_id', $game->away_team_id)->first();
        }

        if (empty($gameComment)) {
            return null;
        }

        $url = route('front.game_comment', $gameComment->uuid);
        $url .= "?pin=" . $gameComment->pin;

        return $url;
    }

    public function liveMatches() {
        return view('front.pages.live_matches');
    }

    public function today() {
        $cache_key = "today_games_cache";
        $cached_data = Cache::store('file')->get($cache_key);
        if (!empty($cached_data))
            return view('front.pages.today', $cached_data);
        else
            Log::debug("Cache miss for today games, generating new one");

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

        $view_data = [
            'games' => $games,
            'closest' => $closest
        ];
        Cache::store('file')->add($cache_key, $view_data, 60);

        return view('front.pages.today', $view_data);
    }

    public function todayEdit()
    {
        if (!has_permission('score_update'))
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

                if (count($score_reports[$game_id]) > 4)
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

        if (!has_permission('score_update'))
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

        return redirect()->route('games.today_edit');
    }

    public function updateIsFake(Request $request, ScoreReport $report) {
        $this->validate($request, [
            'is_fake' => 'nullable|string|max:10'
        ]);

        $is_fake = $request->input('is_fake', "off") == "on";
        $report->is_fake = $is_fake;
        $report->save();

        // If it was not fake, we don't need to do anything
        if (!$is_fake) {
            return redirect()->back();
        }

        try {
            $report->banUser();
        } catch (\Exception $e) {
            Log::error("Error while trying to ban user: " . $e->getMessage());
        }
    }

    public function listScoreReports(Request $request, Game $game) {

        $backUrl = $request->query('back_to', url()->previous());

        $reports = ScoreReport::where('game_id', $game->id)
            ->orderBy('id', 'desc')
            ->paginate(200);

        return view('front.pages.game_reports_list', [
            'game' => $game,
            'reports' => $reports,
            'backUrl' => $backUrl
        ]);
    }

    public function scoresAggregator(Request $request) {
        $partners = Partner::where('visible', true)
            ->orderBy('priority', 'asc')
            ->get();

        return view('front.pages.scores_aggregator', [
            'partners' => $partners
        ]);
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
