<?php

namespace App\Http\Controllers\Front;

use App\GameComment;
use App\Goal;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

class GameCommentsController extends Controller
{
    public function edit(Request $request, string $uuid)
    {
        if (empty($uuid))
            abort(404);

        $gameComment = GameComment::where('uuid', $uuid)->first();
        if (!$gameComment)
            abort(404);

        // Query pin from query parameter
        $pin = $request->query('pin');
        if (empty($pin))
            return redirect()->route('front.game_comment_pin', ['uuid' => $uuid]);

        if ($pin !== $gameComment->pin)
            return redirect()->route('front.game_comment_pin', ['uuid' => $uuid, 'error' => $pin]);

        if ($gameComment->team_id == $gameComment->game->home_team_id) {
            $amountOfGoals = $gameComment->game->goals_home;
            $goals = $gameComment->game->getHomeGoals();
        } else {
            $amountOfGoals = $gameComment->game->goals_away;
            $goals = $gameComment->game->getAwayGoals();
        }

        $deadline = Carbon::createFromFormat('Y-m-d H:i:s', $gameComment->deadline);
        $now = Carbon::now();
        $canEdit = $now->lessThan($deadline);
        $gameDate = Carbon::createFromFormat('Y-m-d H:i:s', $gameComment->game->date)->format('d/m/Y');

        return view(
            'front.pages.edit_game_comment',
            [
                'uuid' => $uuid,
                'pin' => $pin,
                'gameComment' => $gameComment,
                'game' => $gameComment->game,
                'team' => $gameComment->team,
                'amountOfGoals' => $amountOfGoals,
                'recipientClubName' => $gameComment->team->club->name,
                'deadline' => $deadline->timezone('Europe/Lisbon')->format('d/m/Y \à\s H:i'),
                'canEdit' => $canEdit,
                'gameDate' => $gameDate,
                'players' => $gameComment->team->players,
                'goals' => $goals,
            ]
        );
    }

    public function update(Request $request, string $uuid)
    {
        $pin = $request->input('pin', '0000');
        $messages = new MessageBag();
        $redirectRoute = route('front.game_comment', ['uuid' => $uuid]);
        $redirectRoute .= "?pin=$pin";

        $validator = Validator::make($request->all(), [
            'pin' => 'nullable|numeric',
            'content' => 'nullable|string|max:1000',
            'players' => 'nullable|array',
            'goals' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect($redirectRoute)
                ->withErrors($validator)
                ->withInput();
        }

        $gameComment = GameComment::where('uuid', $uuid)->first();
        if (!$gameComment)
            abort(404);

        if ($pin !== $gameComment->pin)
            return redirect()->route('front.game_comment_pin', ['uuid' => $uuid, 'error' => $pin]);

        // If passed deadline, return error
        $now = Carbon::now();
        $deadline = Carbon::createFromFormat('Y-m-d H:i:s', $gameComment->deadline);
        if ($now->greaterThan($deadline)) {
            $messages->add('error', 'Já passou o prazo para editar o comentário.');
            return redirect($redirectRoute)->with(['popup_message' => $messages]);
        }

        // Deal with comment
        $content = Str::limit($request->input('content'), 1000);
        $content = strip_tags($content);
        $gameComment->content = $content;
        $gameComment->save();

        $this->handleGoals($request, $gameComment);
        $messages->add('success', 'Informações guardadas com sucesso!');

        return redirect($redirectRoute)->with(['popup_message' => $messages]);
    }

    private function handleGoals(Request $request, GameComment $gameComment)
    {
        $game = $gameComment->game;
        if ($gameComment->team_id === $game->home_team_id) {
            $savedTeamGoals = $game->getHomeGoals();
            $amountOfGoals = $game->goals_home;
            $teamId = $game->home_team_id;
        } else {
            $savedTeamGoals = $game->getAwayGoals();
            $amountOfGoals = $game->goals_away;
            $teamId = $game->away_team_id;
        }

        if (empty($savedTeamGoals)) {
            $savedTeamGoals = [];
        }

        $formPlayers = $request->input('players', []);
        $formMinutes = $request->input('minutes', []);

        for ($i = 0; $i < $amountOfGoals; $i++) {
            if (!isset($savedTeamGoals[$i])) {
                $savedTeamGoals[$i] = Goal::create([
                    'player_id' => isset($formPlayers[$i]) && $formPlayers[$i] > 0 ? $formPlayers[$i] : null,
                    'team_id' => $teamId,
                    'game_id' => $game->id,
                    'own_goal' => isset($formPlayers[$i]) && $formPlayers[$i] == -1,
                    'penalty' => false,
                    'minute' => isset($formMinutes[$i]) && $formMinutes[$i] > 0 ? $formMinutes[$i] : null,
                    'visible' => true,
                ]);

                continue;
            }

            $savedTeamGoals[$i]->player_id = isset($formPlayers[$i]) && $formPlayers[$i] > 0 ? $formPlayers[$i] : null;
            $savedTeamGoals[$i]->own_goal = isset($formPlayers[$i]) && $formPlayers[$i] == -1;
            $savedTeamGoals[$i]->minute = isset($formMinutes[$i]) && $formMinutes[$i] > 0 ? $formMinutes[$i] : null;
            $savedTeamGoals[$i]->save();
        }
    }

    public function pin(Request $request, string $uuid)
    {
        $error = $request->query('error');

        return view('front.pages.edit_game_comment_pin', [
            'error' => $error,
            'uuid' => $uuid,
        ]);
    }

    public function manageNotifications(Request $request, string $uuid) {

        $gameComment = GameComment::where('uuid', $uuid)->first();
        if (!$gameComment)
            abort(404);

        // Query pin from query parameter
        $pin = $request->query('pin');
        if (empty($pin) || $pin !== $gameComment->pin)
            abort(404);

        $club = $gameComment->team->club;

        return view('front.pages.manage_notifications', [
            'club' => $club,
            'uuid' => $uuid,
            'pin' => $pin,
        ]);
    }

    public function saveManageNotifications(Request $request, string $uuid) {
        $gameComment = GameComment::where('uuid', $uuid)->first();
        if (!$gameComment)
            abort(404);

        $messages = new MessageBag();

        // Query pin from query parameter
        $pin = $request->input('pin');
        if (empty($pin) || $pin !== $gameComment->pin) {
            $messages->add('error', 'Pin inválido!');
            return redirect()->back()->with(['popup_message' => $messages]);
        }

        $club = $gameComment->team->club;
        $club->notifications_enabled = $request->filled('notifications_enabled');
        $club->save();

        $messages->add('success', 'Guardado com sucesso!');

        return redirect()->back()->with(['popup_message' => $messages]);
    }
}
