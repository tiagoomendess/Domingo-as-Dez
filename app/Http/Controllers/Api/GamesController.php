<?php

namespace App\Http\Controllers\Api;

use App\Game;
use App\GameGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GamesController extends Controller
{
    public function show($id) {

        $game = Game::findOrFail($id);

        if (!$game->visible)
            return abort(404);

        $data_object = new \stdClass();
        $data_object->data = new \stdClass();
        $data_object->data->home_score = $game->getHomeScore();
        $data_object->data->away_score = $game->getAwayScore();

        if ($game->decidedByPenalties()) {
            $data_object->data->penalties = '(' . trans('front.after_penalties', ['penalties_home' => $game->penalties_home, 'penalties_away' => $game->penalties_away]) . ')';
        } else {
            $data_object->data->penalties = null;
        }

        $data_object->data->finished = (boolean)$game->finished;

        foreach ($game->getHomeGoals()->sortBy('minute') as $goal) {

            $goal_object = new \stdClass();
            $goal_object->player_name = $goal->getPlayerName();
            $goal_object->player_nickname = $goal->getPlayerNickName();
            $goal_object->player_id = $goal->getPlayerId();
            $goal_object->minute = $goal->minute;
            $goal_object->picture = $goal->getPlayerPicture();

            $data_object->data->home_goals[] = $goal_object;

        }

        foreach ($game->getAwayGoals()->sortBy('minute') as $goal) {

            $goal_object = new \stdClass();
            $goal_object->player_name = $goal->getPlayerName();
            $goal_object->player_nickname = $goal->getPlayerNickName();
            $goal_object->player_id = $goal->getPlayerId();
            $goal_object->minute = $goal->minute;
            $goal_object->picture = $goal->getPlayerPicture();

            $data_object->data->away_goals[] = $goal_object;

        }

        return response()->json($data_object);
    }

    public function getLiveMatches() {
        $return_object = new \stdClass();
        $return_object->data = [];

        $now = Carbon::now()->addHours(1);//Now retorna em UTC, +1 para GMT
        $warmup_date = Carbon::now()->addHours(1)->subMinutes(30);

        $games = Game::getLiveGames();

        $games_by_group = $games->groupBy('game_group_id');

        $i = 0;
        foreach ($games_by_group as $group) {

            $return_object->data[$i] = new \stdClass();
            $return_object->data[$i]->competition_name = $group[0]->game_group->season->competition->name;
            $return_object->data[$i]->competition_logo = $group[0]->game_group->season->competition->picture;
            $return_object->data[$i]->season_name = $group[0]->game_group->season->getName();
            $return_object->data[$i]->games = [];

            $j = 0;
            foreach ($group as $game) {

                $new_game = new \stdClass();
                $new_game->id = $game->id;
                $new_game->game_link = $game->getPublicUrl();
                $new_game->date = $game->date;
                $new_game->started = $game->started();
                $new_game->finished = (boolean)$game->finished;

                $new_game->home_club_name = $game->home_team->club->name;
                $new_game->home_club_emblem = $game->home_team->club->getEmblem();
                $new_game->home_team_name = $game->home_team->name;
                $new_game->home_score = $game->getHomeScore();

                $new_game->away_club_name = $game->away_team->club->name;
                $new_game->away_club_emblem = $game->home_team->club->getEmblem();
                $new_game->away_team_name = $game->away_team->name;
                $new_game->away_score = $game->getAwayScore();

                if ($game->decidedByPenalties()) {
                    $new_game->penalties = '(' . trans('front.after_penalties', ['penalties_home' => $game->penalties_home, 'penalties_away' => $game->penalties_away]) . ')';
                } else {
                    $new_game->penalties = null;
                }

                $return_object->data[$i]->games[$j] = $new_game;
                $j++;
            }

            $i++;
        }

        return response()->json($return_object);
    }
}
