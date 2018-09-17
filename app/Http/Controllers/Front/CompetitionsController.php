<?php

namespace App\Http\Controllers\Front;

use App\Competition;
use App\Game;
use App\Season;
use App\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class CompetitionsController extends Controller
{

    public function show($slug) {

        $competition = Competition::getCompetitionBySlug($slug);

        if(!$competition || !$competition->visible)
            abort(404);

        return view('front.pages.competition', ['competition' => $competition]);

    }

    public function showAll() {

        $competitions = Competition::where('visible', true)->get();

        return view('front.pages.competitions', ['competitions' => $competitions]);
    }

}
