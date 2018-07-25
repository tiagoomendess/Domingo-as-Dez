<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Game;
use App\Season;
use App\Competition;
use Carbon\Carbon;

class CompetitionsController extends Controller
{

    public function getCompetitionSeasons($id) {

        $competition = Competition::findOrFail($id);

        if(!$competition || !$competition->visible)
            abort(404);

        $seasons = $competition->seasons;

        $seasons = $seasons->sortByDesc('start_year');
        $data_object = [];

        $i = 0;
        foreach ($seasons as $season) {

            if ($season->visible) {

                $data_object[$i] = new \stdClass();
                $data_object[$i]->id = $season->id;
                $data_object[$i]->name = $season->getName();
                $data_object[$i]->start_year = $season->start_year;
                $data_object[$i]->end_year = $season->end_year;
                $data_object[$i]->obs = $season->obs;

                $i++;
            }
        }

        return response()->json($data_object);

    }

    public function getCompetitions() {

        $competitions = Competition::where('visible', true)->get();

        $data_object = new \stdClass();

        $i = 0;
        foreach ($competitions as $competition) {

            $data_object->competitions[$i] = new \stdClass();

            $data_object->competitions[$i]->id = $competition->id;
            $data_object->competitions[$i]->name = $competition->name;
            $data_object->competitions[$i]->logo = $competition->picture;
            $data_object->competitions[$i]->slug = str_slug($competition->name);

            $i++;

        }

        return response()->json($data_object);
    }

    /**
     * Gets the season table for the provided season and round
     */
    public function getTable($slug, $season, $round) {

    }
}
