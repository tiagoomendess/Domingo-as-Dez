<?php

namespace App\Http\Controllers\Api;

use App\PlayerUpdateRequest;
use App\Player;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PlayerUpdateRequestsController extends Controller
{
    public function __construct()
    {
        $this->middleware('authenticate.access_token')->only(['store']);
    }

    /**
     * Store a new player update/create request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $createRules = [
            'player_id' => 'nullable',
            'name' => 'required|string|max:155|min:3',
            'picture_url' => 'required|string|max:280|url',
            'club_name' => 'required|string|max:155|min:3',
            'nickname' => 'nullable|string|max:155|min:2',
            'association_id' => 'nullable|string|max:155',
            'phone' => 'nullable|string|max:14|min:6',
            'email' => 'nullable|string|max:100|min:3|email',
            'facebook_profile' => 'nullable|string|max:280|url',
            'birth_date' => 'nullable|date',
            'position' => ['nullable', 'string', Rule::in(['none', 'striker', 'midfielder', 'defender', 'goalkeeper'])],
            'obs' => 'nullable|string|max:3000|min:6',
            'created_by' => 'nullable|string|max:100',
            'source_data' => 'nullable|array'
        ];
        
        $updateRules = [
            'player_id' => 'required|integer|exists:players,id',
            'name' => 'nullable|string|max:155|min:3',
            'nickname' => 'nullable|string|max:155|min:2',
            'club_name' => 'nullable|string|max:155|min:3',
            'picture_url' => 'nullable|string|max:280|url',
            'association_id' => 'nullable|string|max:155',
            'phone' => 'nullable|string|max:14|min:6',
            'email' => 'nullable|string|max:100|min:3|email',
            'facebook_profile' => 'nullable|string|max:280|url',
            'birth_date' => 'nullable|date',
            'position' => ['nullable', 'string', Rule::in(['none', 'striker', 'midfielder', 'defender', 'goalkeeper'])],
            'obs' => 'nullable|string|max:3000|min:6',
            'created_by' => 'nullable|string|max:100',
            'source_data' => 'nullable|array'
        ];

        // Determine if this is a create or update request
        $isCreateRequest = !$request->has('player_id') || $request->player_id === null;
        $validator = Validator::make($request->all(), $isCreateRequest ? $createRules : $updateRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        if ($this->isRecentDuplicateRequest($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Recent duplicate request, did not add another'
            ], 200);
        }

        // Check if player exists
        $player = Player::where('id', $request->player_id)
            ->first();

        if (!$player && !$isCreateRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found'
            ], 404);
        }

        // Create the request (update or create)
        $updateRequest = PlayerUpdateRequest::create([
            'player_id' => $request->player_id, // Will be null for create requests
            'name' => $request->name,
            'nickname' => $request->nickname,
            'club_name' => $request->club_name,
            'picture_url' => $request->picture_url,
            'association_id' => $request->association_id,
            'phone' => $request->phone,
            'email' => $request->email,
            'facebook_profile' => $request->facebook_profile,
            'birth_date' => $request->birth_date,
            'position' => $request->position,
            'obs' => $request->obs,
            'created_by' => $request->created_by ?: 'API',
            'source_data' => $request->source_data,
            'status' => PlayerUpdateRequest::STATUS_PENDING
        ]);

        $messageType = $isCreateRequest ? 'creation' : 'update';
        
        return response()->json([
            'success' => true,
            'message' => "Player {$messageType} request created successfully",
            'data' => [
                'id' => $updateRequest->id,
                'player_id' => $updateRequest->player_id,
                'request_type' => $isCreateRequest ? 'create' : 'update',
                'status' => $updateRequest->status,
                'created_at' => $updateRequest->created_at->toISOString()
            ]
        ], 201);
    }

    // prevent recent duplicate requests
    private function isRecentDuplicateRequest($updateRequest) {
        $recentRequests = PlayerUpdateRequest::where('player_id', $updateRequest->player_id)
            ->where('created_at', '>', now()->subDays(10))
            ->where('name', $updateRequest->name)
            ->where('nickname', $updateRequest->nickname)
            ->where('club_name', $updateRequest->club_name)
            ->get();

        return $recentRequests->count() > 0;
    }

    /**
     * Get the status of a player update request
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $updateRequest = PlayerUpdateRequest::find($id);

        if (!$updateRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Player update request not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $updateRequest->id,
                'player_id' => $updateRequest->player_id,
                'status' => $updateRequest->status,
                'created_by' => $updateRequest->created_by,
                'reviewed_at' => $updateRequest->reviewed_at ? $updateRequest->reviewed_at->toISOString() : null,
                'created_at' => $updateRequest->created_at->toISOString(),
                'updated_at' => $updateRequest->updated_at->toISOString()
            ]
        ]);
    }
} 