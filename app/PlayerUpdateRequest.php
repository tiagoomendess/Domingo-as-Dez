<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Resources\MediaController;

class PlayerUpdateRequest extends SearchableModel
{
    protected $fillable = [
        'player_id', 'name', 'nickname', 'club_name', 'picture_url', 'association_id', 
        'phone', 'email', 'facebook_profile', 'birth_date', 'position', 'obs',
        'status', 'created_by', 'reviewed_by', 'reviewed_at', 'review_notes', 'source_data'
    ];

    protected $guarded = [];

    protected $hidden = [];

    protected $casts = [
        'source_data' => 'array'
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DENIED = 'denied';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pendente',
        self::STATUS_APPROVED => 'Aprovado',
        self::STATUS_DENIED => 'Negado'
    ];

    public const SEARCH_FIELDS = [
        'id' => [
            'name' => 'id',
            'type' => 'integer',
            'trans' => 'Id',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|integer'
        ],
        'player_id' => [
            'name' => 'player_id',
            'type' => 'integer',
            'trans' => 'Player Id',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|integer'
        ],
        'name' => [
            'name' => 'name',
            'type' => 'string',
            'trans' => 'Nome',
            'allowSearch' => true,
            'compare' => 'like',
            'validation' => 'nullable|min:3|max:30|string'
        ],
        'nickname' => [
            'name' => 'nickname',
            'type' => 'string',
            'trans' => 'Alcunha',
            'allowSearch' => true,
            'compare' => 'like',
            'validation' => 'nullable|min:3|max:30|string'
        ],
        'club_name' => [
            'name' => 'club_name',
            'type' => 'string',
            'trans' => 'Clube',
            'allowSearch' => true,
            'compare' => 'like',
            'validation' => 'nullable|min:3|max:30|string'
        ],
        'status' => [
            'name' => 'status',
            'type' => 'enum',
            'trans' => 'Estado',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|in:pending,approved,denied',
            'enumItems' => [
                [
                    'name' => 'Pendente',
                    'value' => 'pending'
                ],
                [
                    'name' => 'Aprovado',
                    'value' => 'approved'
                ],
                [
                    'name' => 'Negado',
                    'value' => 'denied'
                ]
            ]
        ],
        'created_by' => [
            'name' => 'created_by',
            'type' => 'string',
            'trans' => 'Criado Por',
            'allowSearch' => true,
            'compare' => 'like',
            'validation' => 'nullable|string'
        ],
        'created_at' => [
            'name' => 'created_at',
            'type' => 'date',
            'trans' => 'Data de Criação',
            'allowSearch' => false
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'type' => 'date',
            'trans' => 'Ultima Atualização',
            'allowSearch' => false
        ]
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayName()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Check if the request is pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the request is approved
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the request is denied
     */
    public function isDenied()
    {
        return $this->status === self::STATUS_DENIED;
    }

    /**
     * Check if this is a player creation request
     */
    public function isCreateRequest()
    {
        return $this->player_id === null;
    }

    /**
     * Check if this is a player update request
     */
    public function isUpdateRequest()
    {
        return $this->player_id !== null;
    }

    /**
     * Process and store the picture from URL
     * Returns the processed picture URL or throws exception if processing fails
     */
    private function processPicture()
    {
        if (!$this->picture_url) {
            return null;
        }

        try {
            // Download and create image from URL
            $image = Image::make($this->picture_url);
            
            // Get the name for the image (use player name if available, or request name)
            $name = $this->name;
            if ($this->isUpdateRequest() && $this->player) {
                $name = $this->player->name;
            }

            // Store the square image (same logic as PlayerController)
            $url = MediaController::storeSquareImage($image, $name);

            // Create Media record with tags
            $tags = trans('models.player') . ',' . $name;
            if ($this->nickname) {
                $tags = $tags . ',' . $this->nickname;
            }

            $media = \App\Media::create([
                'user_id' => Auth::id(),
                'url' => $url,
                'media_type' => 'image',
                'tags' => $tags,
            ]);

            // Generate thumbnail
            $media->generateThumbnail();

            return $url;
        } catch (\Exception $e) {
            throw new \Exception('Failed to process picture: ' . $e->getMessage());
        }
    }

    /**
     * Delete old picture if it exists
     */
    private function deleteOldPicture($oldPictureUrl)
    {
        if (!$oldPictureUrl) {
            return;
        }

        try {
            // Find and delete the Media record
            $media = \App\Media::where('url', $oldPictureUrl)->first();
            if ($media) {
                $media->delete();
            }

            // Delete the physical file if it exists
            $filePath = public_path(ltrim($oldPictureUrl, '/'));
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Also delete thumbnail if it exists
            $thumbnailPath = str_replace('/images/', '/images/thumbs/', $filePath);
            if (file_exists($thumbnailPath)) {
                unlink($thumbnailPath);
            }
        } catch (\Exception $e) {
            // Log the error but don't throw - we don't want to fail the approval just because we can't delete old files
            \Log::warning('Failed to delete old picture: ' . $e->getMessage());
        }
    }

    /**
     * Approve the request and update/create the player
     */
    public function approve($reviewNotes = null)
    {
        // Process picture FIRST - if this fails, nothing else gets updated
        $processedPictureUrl = null;
        if ($this->picture_url) {
            $processedPictureUrl = $this->processPicture();
        }

        $this->status = self::STATUS_APPROVED;
        $this->reviewed_by = Auth::id();
        $this->reviewed_at = Carbon::now();
        $this->review_notes = $reviewNotes;
        $this->save();

        if ($this->isCreateRequest()) {
            $this->createPlayer($processedPictureUrl);
        } else {
            $this->updatePlayer($processedPictureUrl);
        }

        // Create audit entry
        Audit::add(Audit::ACTION_UPDATE, 'PlayerUpdateRequest', null, $this->toArray());
    }

    /**
     * Deny the request
     */
    public function deny($reviewNotes = null)
    {
        $this->status = self::STATUS_DENIED;
        $this->reviewed_by = Auth::id();
        $this->reviewed_at = Carbon::now();
        $this->review_notes = $reviewNotes;
        $this->save();

        // Create audit entry
        Audit::add(Audit::ACTION_UPDATE, 'PlayerUpdateRequest', null, $this->toArray());
    }

    /**
     * Create a new player from the request data
     */
    private function createPlayer($processedPictureUrl = null)
    {
        // Create the player
        $player = Player::create([
            'name' => $this->name,
            'nickname' => $this->nickname,
            'picture' => $processedPictureUrl,
            'association_id' => $this->association_id,
            'phone' => $this->phone,
            'email' => $this->email,
            'facebook_profile' => $this->facebook_profile,
            'birth_date' => $this->birth_date,
            'position' => $this->position ?? 'none',
            'obs' => $this->obs,
            'visible' => true
        ]);

        // Update the request to link to the newly created player
        $this->player_id = $player->id;
        $this->save();

        // Create transfer if club_name is provided
        if ($this->club_name) {
            $this->createTransfer($player, $this->club_name);
        }

        // Create audit entry for the player creation
        Audit::add(Audit::ACTION_CREATE, 'Player', null, $player->toArray());
    }

    /**
     * Update an existing player from the request data
     */
    private function updatePlayer($processedPictureUrl = null)
    {
        $player = $this->player;
        if (!$player) {
            throw new \Exception('Player not found for update request');
        }

        $oldPlayerData = $player->toArray();
        $needsClubTransfer = false;
        $currentClub = $player->getClub();
        $currentClubName = $currentClub ? $currentClub->name : null;

        // Check if club is changing
        if ($this->club_name && $this->club_name !== $currentClubName) {
            $needsClubTransfer = true;
        }

        // Handle picture update and deletion of old picture
        if ($processedPictureUrl) {
            // Delete old picture if it exists
            $oldPictureUrl = $player->picture;
            if ($oldPictureUrl) {
                $this->deleteOldPicture($oldPictureUrl);
            }
            $player->picture = $processedPictureUrl;
        }

        // Update only the fields that have values in the request
        if ($this->name) {
            $player->name = $this->name;
        }
        if ($this->nickname) {
            $player->nickname = $this->nickname;
        }
        if ($this->association_id) {
            $player->association_id = $this->association_id;
        }
        if ($this->phone) {
            $player->phone = $this->phone;
        }
        if ($this->email) {
            $player->email = $this->email;
        }
        if ($this->facebook_profile) {
            $player->facebook_profile = $this->facebook_profile;
        }
        if ($this->birth_date) {
            $player->birth_date = $this->birth_date;
        }
        if ($this->position) {
            $player->position = $this->position;
        }

        $player->save();

        // Create transfer if club is changing
        if ($needsClubTransfer) {
            $this->createTransfer($player, $this->club_name);
        }

        // Create audit entry for the player update
        Audit::add(Audit::ACTION_UPDATE, 'Player', $oldPlayerData, $player->toArray());
    }

    /**
     * Create a transfer for the player to the specified club
     */
    private function createTransfer($player, $clubName)
    {
        if ($clubName == "none" || $clubName == "no_club") {
            $this->createTransferToNoClub($player);
            return;
        }

        // Find the club by name
        $club = Club::where('name', $clubName)->first();
        
        if (!$club) {
            throw new \Exception("Club '{$clubName}' not found");
        }

        // Find teams for this club
        $teams = $club->teams()->where('visible', true)->get();
        
        if ($teams->isEmpty()) {
            throw new \Exception("No teams found for club '{$clubName}'");
        }

        // Try to find a team with "Séniores" in the name
        $team = $teams->where('name', 'like', '%Séniores%')->first();
        
        // If no "Séniores" team found, use the first team
        if (!$team) {
            $team = $teams->first();
        }

        // Create the transfer using the request's creation date
        $transfer = Transfer::create([
            'player_id' => $player->id,
            'team_id' => $team->id,
            'date' => $this->created_at->format('Y-m-d H:i:s'),
            'visible' => true
        ]);

        // Create audit entry for the transfer
        Audit::add(Audit::ACTION_CREATE, 'Transfer', null, $transfer->toArray());
    }

    private function createTransferToNoClub($player)
    {
        $transfer = Transfer::create([
            'player_id' => $player->id,
            'team_id' => null,
            'date' => $this->created_at->format('Y-m-d H:i:s'),
            'visible' => true
        ]);

        // Create audit entry for the transfer
        Audit::add(Audit::ACTION_CREATE, 'Transfer', null, $transfer->toArray());
    }

    /**
     * Get changes as a readable array
     */
    public function getChanges()
    {
        $changes = [];
        
        // For create requests, return all non-null fields as "new" values
        if ($this->isCreateRequest()) {
            if ($this->name) {
                $changes['name'] = ['old' => null, 'new' => $this->name];
            }
            if ($this->nickname) {
                $changes['nickname'] = ['old' => null, 'new' => $this->nickname];
            }
            if ($this->picture_url) {
                $changes['picture'] = ['old' => null, 'new' => $this->picture_url];
            }
            if ($this->association_id) {
                $changes['association_id'] = ['old' => null, 'new' => $this->association_id];
            }
            if ($this->phone) {
                $changes['phone'] = ['old' => null, 'new' => $this->phone];
            }
            if ($this->email) {
                $changes['email'] = ['old' => null, 'new' => $this->email];
            }
            if ($this->facebook_profile) {
                $changes['facebook_profile'] = ['old' => null, 'new' => $this->facebook_profile];
            }
            if ($this->birth_date) {
                $changes['birth_date'] = ['old' => null, 'new' => $this->birth_date];
            }
            if ($this->position) {
                $changes['position'] = ['old' => null, 'new' => $this->position];
            }
            if ($this->club_name) {
                $changes['club_name'] = ['old' => null, 'new' => $this->club_name];
            }
            return $changes;
        }

        // For update requests, compare with existing player
        $player = $this->player;
        if (!$player) {
            return $changes;
        }

        if ($this->name && $this->name !== $player->name) {
            $changes['name'] = ['old' => $player->name, 'new' => $this->name];
        }
        if ($this->nickname && $this->nickname !== $player->nickname) {
            $changes['nickname'] = ['old' => $player->nickname, 'new' => $this->nickname];
        }
        if ($this->picture_url && $this->picture_url !== $player->picture) {
            $changes['picture'] = ['old' => $player->picture, 'new' => $this->picture_url];
        }
        if ($this->association_id && $this->association_id !== $player->association_id) {
            $changes['association_id'] = ['old' => $player->association_id, 'new' => $this->association_id];
        }
        if ($this->phone && $this->phone !== $player->phone) {
            $changes['phone'] = ['old' => $player->phone, 'new' => $this->phone];
        }
        if ($this->email && $this->email !== $player->email) {
            $changes['email'] = ['old' => $player->email, 'new' => $this->email];
        }
        if ($this->facebook_profile && $this->facebook_profile !== $player->facebook_profile) {
            $changes['facebook_profile'] = ['old' => $player->facebook_profile, 'new' => $this->facebook_profile];
        }
        if ($this->birth_date && $this->birth_date !== $player->birth_date) {
            $changes['birth_date'] = ['old' => $player->birth_date, 'new' => $this->birth_date];
        }
        if ($this->position && $this->position !== $player->position) {
            $changes['position'] = ['old' => $player->position, 'new' => $this->position];
        }
        if ($this->club_name) {
            $currentClub = $player->getClub();
            $currentClubName = $currentClub ? $currentClub->name : null;
            if ($this->club_name !== $currentClubName) {
                $newClub = $this->club_name === "no_club" ? "Sem Clube" : $this->club_name;
                $changes['club_name'] = ['old' => $currentClubName, 'new' => $newClub];
            }
        }

        return $changes;
    }
}
