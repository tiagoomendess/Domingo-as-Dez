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

        $now = Carbon::now();
        $warmup_date = Carbon::now()->subMinutes(30);

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

                preg_match("/[A-Z\Á][a-z\ç\ã\õ\á]+$/", $game->home_team->club->name,$small_name_match);
                if (count($small_name_match) > 0)
                    $new_game->home_club_name_small = str_replace("...", "", str_limit($small_name_match[0], 3));
                else
                    $new_game->home_club_name_small = str_replace("...", "", str_limit($game->home_team->club->name, 3));

                preg_match("/[A-Z\Á][a-z\ç\ã\õ\á]+$/", $game->away_team->club->name,$small_name_match);
                if (count($small_name_match) > 0)
                    $new_game->away_club_name_small = str_replace("...", "", str_limit($small_name_match[0], 3));
                else
                    $new_game->away_club_name_small = str_replace("...", "", str_limit($game->away_team->club->name, 3));

                $new_game->home_club_name_small = strtoupper($new_game->home_club_name_small);
                $new_game->away_club_name_small = strtoupper($new_game->away_club_name_small);

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
                $new_game->away_club_emblem = $game->away_team->club->getEmblem();
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

    public function isLive() {

        $data = new \stdClass();

        if (count(Game::getLiveGames()) > 0)
            $data->is_live = true;
        else
            $data->is_live = false;

        return response()->json($data);

    }

    public function updateScoreLiveMatch(Request $request) {

        $local_token = 'inuVeIZB5IjoxXiMDdnf';
        $out = new \stdClass();
        $out->success = false;

        $token = $request->json('token');

        if ($token != $local_token) {
            $out->error_message = 'Invalid Token!';
            return response()->json($out);
        }

        $home_club = $request->json('home_club');
        $home_score = $request->json('home_score');
        $away_club = $request->json('away_club');
        $away_score = $request->json('away_score');

        $games = Game::getLiveGames();

        if (count($games) == 0) {
            $out->error_message = 'No live matches!';
            return response()->json($out);
        }

        $percent1 = null;
        $percent2 = null;
        foreach ($games as $game) {

            similar_text(strtolower($game->home_team->club->name), $home_club,$percent1);
            similar_text(strtolower($game->away_team->club->name), $away_club,$percent2);

            if ($percent1 > 80 && $percent2 > 80) {
                $game->goals_home = $home_score;
                $game->goals_away = $away_score;
                $game->save();
                $out->success = true;
                $out->message = $game->home_team->club->name . ' vs ' . $game->away_team->club->name . ' alterado! ID: ' . $game->id;
                break;
            }

        }

        return response()->json($out);


    }
}
