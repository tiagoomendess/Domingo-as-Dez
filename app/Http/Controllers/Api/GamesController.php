<?php

namespace App\Http\Controllers\Api;

use App\Game;
use App\Http\Controllers\Controller;
use App\ScoreReport;
use App\Variable;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GamesController extends Controller
{
    public function __construct()
    {
        $this->middleware('authenticate.access_token')->only(['scoreboardUpdated']);
    }

    public function show($id)
    {

        $game = Game::findOrFail($id);

        if (!$game->visible)
            return abort(404);

        $data_object = new \stdClass();
        $data_object->data = new \stdClass();
        $data_object->data->home_score = $game->getHomeScore();
        $data_object->data->away_score = $game->getAwayScore();
        $data_object->data->home_emblem = $game->home_team->club->getEmblem();
        $data_object->data->away_emblem = $game->away_team->club->getEmblem();
        $data_object->data->home_name = $game->home_team->club->name;
        $data_object->data->away_name = $game->away_team->club->name;

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

    public function getLiveMatches()
    {
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

                preg_match("/[A-Z\Á][a-z\ç\ã\õ\á]+$/", $game->home_team->club->name, $small_name_match);
                if (count($small_name_match) > 0)
                    $new_game->home_club_name_small = str_replace("...", "", Str::limit($small_name_match[0], 3));
                else
                    $new_game->home_club_name_small = str_replace("...", "", Str::limit($game->home_team->club->name, 3));

                preg_match("/[A-Z\Á][a-z\ç\ã\õ\á]+$/", $game->away_team->club->name, $small_name_match);
                if (count($small_name_match) > 0)
                    $new_game->away_club_name_small = str_replace("...", "", Str::limit($small_name_match[0], 3));
                else
                    $new_game->away_club_name_small = str_replace("...", "", Str::limit($game->away_team->club->name, 3));

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

    public function getNextTeamGame(int $team_id)
    {
        $now = Carbon::now('Europe/Lisbon');

        $gameHome = Game::where('date', '>', $now)
            ->where('visible', true)
            ->where('home_team_id', $team_id)
            ->orderBy('date', 'asc')
            ->first();

        $gameAway = Game::where('date', '>', $now)
            ->where('visible', true)
            ->where('away_team_id', $team_id)
            ->orderBy('date', 'asc')
            ->first();

        $return_object = new \stdClass();
        $game = null;

        if (!$gameHome && !$gameAway) {
            $return_object->has_game = false;
        } else {

            if (!empty($gameHome) && !empty($gameAway)) {
                $game = $gameAway->date < $gameHome->date ? $gameAway : $gameHome;
            }

            if (empty($game)) {
                $game = $gameHome ?? $gameAway;
            }

            $return_object->has_game = true;
            $return_object->home_team = $game->homeTeam->club->name;
            $return_object->home_emblem = $game->homeTeam->club->getEmblem();

            $return_object->away_team = $game->awayTeam->club->name;
            $return_object->away_team_emblem = $game->awayTeam->club->getEmblem();

            $return_object->ground = $game->playground->name;

            $return_object->date = (new DateTime($game->date))->setTimezone(new \DateTimeZone('Europe/Lisbon'))->format('Y/m/d H:i');
        }

        return response()->json($return_object);
    }

    public function isLive()
    {

        $data = new \stdClass();

        if (count(Game::getLiveGames()) > 0)
            $data->is_live = true;
        else
            $data->is_live = false;

        return response()->json($data);

    }

    public function updateScoreLiveMatch(Request $request)
    {
        abort(404); //not in use, but don't want to throw away the code
        $local_token = 'inuVeIZB5IjoxXiMDdnf';
        $out = new \stdClass();
        $out->success = false;

        $token = $request->json('token');

        if ($token != $local_token) {
            $out->error_message = 'Invalid Token!';
            return response()->json($out);
        }

        $home_club = $request->json('home_club_name');
        $home_score = $request->json('home_club_score');

        $away_club = $request->json('away_club_name');
        $away_score = $request->json('away_club_score');

        if (!is_int($home_score) || !is_int($away_score)) {
            $out->error_message = 'Score must be integer!';
            return response()->json($out);
        }

        $games = Game::getLiveGames();

        if (count($games) == 0) {
            $out->error_message = 'No live matches!';
            return response()->json($out);
        }

        foreach ($games as $game) {

            similar_text(strtolower($game->home_team->club->name), $home_club, $percent1);
            similar_text(strtolower($game->away_team->club->name), $away_club, $percent2);

            $out->percents = $percent1 . ' - ' . $percent2;

            if ($percent1 > 71 && $percent2 > 71) {
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

    public function todayMatches()
    {
        $now = Carbon::now();
        $begin = clone ($now)->startOfDay();
        $end = clone ($now)->endOfDay();

        $games = Game::where('date', '>', $begin)
            ->where('date', '<', $end)
            ->where('visible', true)
            ->orderBy('date', 'asc')
            ->get();

        $results = [];
        foreach ($games as $game) {
            $data_object = new \stdClass();
            $data_object->id = $game->id;
            $data_object->home_score = $game->getHomeScore();
            $data_object->away_score = $game->getAwayScore();
            $data_object->home_emblem = $game->home_team->club->getEmblem();
            $data_object->away_emblem = $game->away_team->club->getEmblem();
            $data_object->home_name = $game->home_team->club->name;
            $data_object->away_name = $game->away_team->club->name;
            $results[] = $data_object;
        }

        $test_game_id = Variable::getValue('test_game_id');
        if (empty($test_game_id)) {
            return response()->json($results);
        }

        $testGame = Game::find($test_game_id);
        if (empty($testGame)) {
            return response()->json($results);
        }

        // Return list with single test game
        $data_object = new \stdClass();
        $data_object->id = $testGame->id;
        $data_object->home_score = $testGame->getHomeScore();
        $data_object->away_score = $testGame->getAwayScore();
        $data_object->home_emblem = $testGame->home_team->club->getEmblem();
        $data_object->away_emblem = $testGame->away_team->club->getEmblem();
        $data_object->home_name = $testGame->home_team->club->name;
        $data_object->away_name = $testGame->away_team->club->name;
        $results[] = $data_object;

        return response()->json($results);
    }

    public function scoreboardUpdated(Request $request, Game $game)
    {
        if (empty($game)) {
            return response()->json([
                'success' => false,
                'message' => 'Jogo não encontrado!'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'home_score' => 'required|min:0|max:99|integer',
            'away_score' => 'required|min:0|max:99|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos!'
            ], 400);
        }

        if (!$game->started() || $game->finished) {
            return response()->json([
                'success' => false,
                'message' => 'O jogo ainda não começou ou já terminou!'
            ], 400);
        }

        $home_score = $request->json('home_score');
        $away_score = $request->json('away_score');
        $current_home_score = $game->getHomeScore();
        $current_away_score = $game->getAwayScore();

        $five_minutes_ago = Carbon::now()->subMinutes(5)->toDateTimeString();
        $existing_report = ScoreReport::where('game_id', $game->id)
            ->where('home_score', $home_score)
            ->where('away_score', $away_score)
            ->where('source', 'placard')
            ->where('created_at', '>', $five_minutes_ago)
            ->count();

        if (empty($existing_report)) {
            ScoreReport::create([
                'game_id' => $game->id,
                'home_score' => $home_score,
                'away_score' => $away_score,
                'source' => 'placard'
            ]);
        }

        if (!$game->scoreboard_updates) {
            return response()->json([
                'success' => false,
                'message' => 'Este jogo não aceita atualizações de resultado via placard!'
            ], 400);
        }

        if ($home_score == $current_home_score && $away_score == $current_away_score) {
            return response()->json([
                'success' => true,
                'message' => 'O resultado já é o que foi enviado, atualização desnecessária!'
            ], 304);
        }

        $game->goals_home = $home_score;
        $game->goals_away = $away_score;
        $game->save();

        return response()->json([
            'success' => true,
            'message' => 'Resultado atualizado com sucesso!'
        ]);
    }
}
