<?php

namespace App\Http\Controllers\Front;

use App\GameReferee;
use App\Referee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RefereesController extends Controller
{
    public function show($id, $name_slug) {

        $referee = Referee::findOrFail($id);

        if ($name_slug != str_slug($referee->name))
            return abort(404);

        if (!$referee->visible)
            return abort(404);

        $game_referees = GameReferee::where('referee_id', $referee->id)->orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('front.pages.referee', ['referee' => $referee, 'game_referees' => $game_referees]);


    }
}
