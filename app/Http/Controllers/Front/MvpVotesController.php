<?php

namespace App\Http\Controllers\Front;

use App\Game;
use App\Http\Controllers\Controller;
use App\MvpVotes;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;

class MvpVotesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function vote(Request $request)
    {
        $request->validate([
            'game' => 'required|Integer',
            'player' => 'required|Integer',
        ]);

        $messages = new MessageBag();

        /** @var User $user */
        $user = Auth::user();
        $game_id = $request->input('game');
        $player_id = $request->input('player');

        $previousVotesCount = MvpVotes::where('game_id', $game_id)
            ->where('user_id', $user->id)->count();

        if ($previousVotesCount > 0) {
            $messages->add('error', 'Não pode votar de novo!');
            return redirect()->back()->with(['popup_message' => $messages]);
        }

        /** @var Game $game */
        $game = Game::find($game_id);

        if ($game->isMvpVoteOpen()) {
            MvpVotes::create([
                'game_id' => $game_id,
                'player_id' => $player_id,
                'user_id' => $user->id
            ]);

            Log::info("User " . $user->id . " voted for player " . $player_id . " for MVP in the game " . $game_id);

            return redirect()->back();
        } else {
            $messages->add('error', 'A janela de voto não está aberta para este jogo');
            return redirect()->back()->with(['popup_message' => $messages]);
        }
    }
}
