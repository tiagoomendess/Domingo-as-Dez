<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Season;

class SeasonsController extends Controller
{
    public function show($season) {

        $season = Season::findOrFail($season);

        if (!$season->visible)
            abort(404);

        $info['data']['id'] = $season->id;
        $info['data']['max_rounds'] = $season->getTotalRounds();
        $info['data']['start_year'] = $season->start_year;
        $info['data']['end_year'] = $season->end_year;
        $info['data']['total_teams'] = $season->getTotalTeams();

        $info['success'] = true;

        return response()->json($info);
    }

    public function getGames($season_id) {

        $season = Season::findOrFail($season_id);

        if (!$season || !$season->visible)
            abort(404);

        $data_object = new \stdClass();

        $game_groups = $season->game_groups;
        $i = 0;

        $data_object->data = new \stdClass();
        $data_object->data->competition_id = $season->competition->id;
        $data_object->data->competition_name = $season->competition->name;
        $data_object->data->season_id = $season->id;
        $data_object->data->season_name = $season->getName();

        foreach ($game_groups as $game_group) {

            $data_object->data->groups[$i] = new \stdClass();
            $data_object->data->groups[$i]->name = $game_group->name;
            $data_object->data->groups[$i]->name_slug = str_slug($game_group->name);

            switch ($game_group->group_rules->type) {
                case 'points':
                    $data_object->data->groups[$i]->round_name = trans('front.matchday');
                    break;
                case 'elimination':
                    $data_object->data->groups[$i]->round_name = trans('front.cup_tie');
                    break;
                case 'friendly':
                    $data_object->data->groups[$i]->round_name = trans('front.friendlies');
                    break;
                default:
                    $data_object->data->groups[$i]->round_name = trans('front.week');
            }

            $data_object->data->groups[$i]->type = $game_group->group_rules->type;
            $data_object->data->groups[$i]->rules_name = $game_group->group_rules->name;
            $data_object->data->groups[$i]->relegates = $game_group->group_rules->relegates;
            $data_object->data->groups[$i]->promotes = $game_group->group_rules->promotes;
            $data_object->data->groups[$i]->rounds = [];

            $game_rounds = $game_group->games->groupBy('round');

            $data_object->data->groups[$i]->rounds = [];

            foreach ($game_rounds as $round) {

                $round_object = new \stdClass();
                $round_object->number = $round->first()->round;

                foreach ($round as $game) {

                    $game_object = new \stdClass();

                    $game_object->date = $game->date;
                    $game_object->playground = $game->playground->name;
                    $game_object->finished = $game->finished;
                    $game_object->game_link = $game->getPublicUrl();

                    $game_object->home_club_name = $game->home_team->club->name;
                    $game_object->home_club_url = $game->home_team->club->getPublicURL();
                    $game_object->home_club_emblem = $game->home_team->club->getEmblem();
                    $game_object->home_team_name = $game->home_team->name;
                    $game_object->home_team_score = $game->getHomeScore();

                    $game_object->away_club_name = $game->away_team->club->name;
                    $game_object->away_club_url = $game->away_team->club->getPublicURL();
                    $game_object->away_club_emblem = $game->away_team->club->getEmblem();
                    $game_object->away_team_name = $game->away_team->name;
                    $game_object->away_team_score = $game->getAwayScore();

                    $round_object->games[] = $game_object;

                }

                $data_object->data->groups[$i]->rounds[] = $round_object;
            }

            $i++;
        }

        return response()->json($data_object);
    }

}
