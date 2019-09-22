<?php

namespace App\Http\Controllers\Front;

use App\Competition;
use App\Game;
use App\MvpVotes;
use App\Player;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GameGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        return view('front.pages.game', ['game' => $game, 'mvp' => $mvp, 'mvp_vote' => $mvpVote]);
    }

    public function liveMatches() {
        return view('front.pages.live_matches');
    }
}


