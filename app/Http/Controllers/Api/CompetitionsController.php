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
                $data_object[$i]->season_slug = $season->getNameSlug();
                $data_object[$i]->competition_slug = $season->getDisplaySlug();
                $data_object[$i]->competition_name = $season->getDisplayName();
                $data_object[$i]->competition_logo = $season->getDisplayPicture();

                $i++;
            }
        }

        return response()->json($data_object);

    }

    /**
     * Competitions for the latest year among visible seasons.
     */
    public function getCompetitions() {
        $latest = Season::where('visible', true)
            ->whereHas('competition', function ($q) {
                $q->where('visible', true);
            })
            ->orderByDesc('start_year')
            ->orderByDesc('end_year')
            ->first();

        if (!$latest) {
            return response()->json([
                'season_slug' => null,
                'start_year' => null,
                'end_year' => null,
                'competitions' => [],
            ]);
        }

        return $this->getCompetitionsBySeasonSlug($latest->getNameSlug());
    }

    /**
     * Competitions that have a visible season matching the season slug.
     */
    public function getCompetitionsBySeasonSlug($season_slug) {
        $years = Season::parseNameSlug($season_slug);
        if (!$years) {
            abort(404);
        }

        [$start_year, $end_year] = $years;

        $seasons = Season::with('competition')
            ->where('visible', true)
            ->where('start_year', $start_year)
            ->where('end_year', $end_year)
            ->whereHas('competition', function ($q) {
                $q->where('visible', true);
            })
            ->get()
            ->sort(function ($a, $b) {
                $priorityA = (int) $a->competition->getAttribute('priority');
                $priorityB = (int) $b->competition->getAttribute('priority');

                if ($priorityA === $priorityB) {
                    return $a->competition->id <=> $b->competition->id;
                }

                return $priorityB <=> $priorityA;
            })
            ->values();

        if ($seasons->isEmpty()) {
            abort(404);
        }

        $canonicalSlug = $seasons->first()->getNameSlug();

        $data_object = new \stdClass();
        $data_object->season_slug = $canonicalSlug;
        $data_object->start_year = $start_year;
        $data_object->end_year = $end_year;
        $data_object->competitions = [];

        $i = 0;
        foreach ($seasons as $season) {
            $data_object->competitions[$i] = new \stdClass();
            $data_object->competitions[$i]->id = $season->competition->id;
            $data_object->competitions[$i]->season_id = $season->id;
            $data_object->competitions[$i]->name = $season->getDisplayName();
            $data_object->competitions[$i]->logo = $season->getDisplayPicture();
            $data_object->competitions[$i]->slug = $season->getDisplaySlug();
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
