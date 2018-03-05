<?php

namespace App\Http\Controllers\Front;

use App\Season;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

}
