<?php

namespace App\Http\Controllers\Api;

use App\Game;
use App\Http\Controllers\Controller;
use App\ScoreReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ScoreReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('authenticate.access_token')->only(['store']);
    }

    public function store(Request $request, Game $game)
    {
        $validatorRules = [
            'user_id' => 'integer|nullable',
            'home_score' => 'required|integer|min:0|max:32',
            'away_score' => 'required|integer|min:0|max:32',
            'latitude' => 'numeric|nullable',
            'longitude' => 'numeric|nullable',
            'accuracy' => 'numeric|nullable',
            'ip_address' => 'string|max:155|nullable',
            'redirect_to' => 'string|max:255|nullable',
            'finished' => 'boolean|nullable',
            'uuid' => 'string|required|max:36|nullable',
        ];

        $validator = Validator::make($request->all(), $validatorRules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Bad Request did not meet validation rules',
                'errors' => $validator->errors(),
            ]);
        }

        $source = $request->input('source', 'unknown');
        $location = $this->getMysqlPoint($request->input('latitude'), $request->input('longitude'));

        ScoreReport::create([
            'user_id' => $request->input('user_id'),
            'game_id' => $game->id,
            'home_score' => $request->input('home_score'),
            'away_score' => $request->input('away_score'),
            'source' => Str::limit("api_$source", 25, ''),
            'ip_address' => Str::limit($request->input('ip_address'), 45, ''),
            'user_agent' => Str::limit($request->header('User-Agent'), 255, ''),
            'location' => $location,
            'location_accuracy' => $request->input('accuracy') ? (int) $request->input('accuracy') : null,
            'uuid' => $request->input('uuid'),
        ]);

        return response()->json([
            'message' => 'Score report created successfully'
        ]);
    }

    private function getMysqlPoint($latitude, $longitude) {
        if (empty($latitude) || empty($longitude)) {
            return null;
        }

        return DB::raw("ST_GeomFromText('POINT($latitude $longitude)')");
    }
}
