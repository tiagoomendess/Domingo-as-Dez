<?php

namespace App\Http\Controllers\Api;

use App\Player;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlayersController extends Controller
{
    public function __construct()
    {
        $this->middleware('authenticate.access_token')->only(['index']);
    }

    /**
     * Get paginated list of players
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100); // Limit to maximum 100 items per page

        $players = Player::where('visible', true)
            ->orderBy('id')
            ->paginate($perPage);

        $playerData = $players->getCollection()->map(function ($player) {
            $club = $player->getClub();
            
            return [
                'id' => $player->id,
                'name' => $player->name,
                'picture' => $player->picture,
                'nickname' => $player->nickname,
                'birth_date' => $player->birth_date,
                'age' => $player->getAge(),
                'current_club_name' => $club ? $club->name : null,
                'association_id' => $player->association_id,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $playerData,
            'pagination' => [
                'current_page' => $players->currentPage(),
                'last_page' => $players->lastPage(),
                'per_page' => $players->perPage(),
                'total' => $players->total(),
                'from' => $players->firstItem(),
                'to' => $players->lastItem(),
            ]
        ]);
    }
}
