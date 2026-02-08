<?php

namespace App\Jobs;

use App\Goal;
use App\IdempotencyRecord;
use App\Media;
use App\Player;
use App\SocialMediaPost;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

class PlayerGoalMilestones implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const STORY_WIDTH = 1080;
    const STORY_HEIGHT = 1920;
    const POST_WIDTH = 1080;
    const POST_HEIGHT = 1350;
    const TEMP_FOLDER = 'storage/tmp/';
    const FB_AND_IG = [SocialMediaPost::PLATFORM_FACEBOOK, SocialMediaPost::PLATFORM_INSTAGRAM];

    const MILESTONES = [50, 75, 100, 125, 150, 200, 300];

    /**
     * Convert a URL path (e.g. /storage/media/images/...) to an absolute file path
     */
    private function urlToFilePath(string $urlPath): string
    {
        $relativePath = ltrim($urlPath, '/');
        return public_path($relativePath);
    }

    /**
     * Get the background image path for a player (their club's stadium)
     */
    private function getBackgroundImagePath(Player $player): string
    {
        $club = $player->getClub();

        if ($club) {
            $playground = $club->getFirstPlayground();
            if ($playground) {
                return $this->urlToFilePath($playground->getPicture());
            }
        }

        // Fallback to a placeholder image
        return $this->urlToFilePath(Media::getPlaceholder('16:9', $player->id));
    }

    /**
     * Get the ImageManager instance with the best available driver.
     * ImageMagick is much faster for mask operations.
     */
    private function getImageManager(): ImageManager
    {
        // Use ImageMagick if available (much faster for mask operations)
        if (extension_loaded('imagick')) {
            return new ImageManager(['driver' => 'imagick']);
        }

        // Fallback to GD
        return new ImageManager(['driver' => 'gd']);
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $startTime = new \DateTime();
        Log::info("Starting PlayerGoalMilestones...");

        try {
            $this->doHandle();
        } catch (\Exception $e) {
            Log::error("Error processing player goal milestones: " . $e->getMessage());
        }

        $endTime = new \DateTime();
        $diffTime = $endTime->diff($startTime);
        $elapsed = $diffTime->format("%s seconds %F microseconds");
        Log::info("PlayerGoalMilestones Job finished in $elapsed.");
    }

    private function doHandle()
    {
        // Get all goals created in the last 24 hours
        $recentGoals = Goal::where('created_at', '>=', Carbon::now()->subDay())->get();

        if ($recentGoals->isEmpty()) {
            Log::info("No goals scored in the last 24 hours.");
            return;
        }

        // Extract unique player IDs
        $playerIds = $recentGoals->pluck('player_id')->unique()->filter();

        Log::info("Found " . $playerIds->count() . " player(s) who scored in the last 24 hours.");

        foreach ($playerIds as $playerId) {
            $player = Player::find($playerId);

            if (!$player) {
                Log::warning("Player with ID $playerId not found.");
                continue;
            }

            $this->processPlayer($player);
        }
    }

    private function processPlayer(Player $player)
    {
        // Get total goals for this player
        $totalGoals = Goal::where('player_id', $player->id)->count();
        $totalGoals = 28;

        Log::info("Player '{$player->displayName()}' has $totalGoals total goals.");

        // Find the highest milestone the player has reached
        $highestMilestone = null;
        foreach (self::MILESTONES as $milestone) {
            if ($totalGoals >= $milestone && $totalGoals <= $milestone + 3) {
                $highestMilestone = $milestone;
            }
        }

        if ($highestMilestone === null) {
            Log::info("Player '{$player->displayName()}' hasn't reached any milestone yet.");
            return;
        }

        $idempotencyKey = "player_{$player->id}_goal_posted_{$highestMilestone}";

        // Check if we've already posted for this milestone
        if (IdempotencyRecord::exists($idempotencyKey)) {
            Log::info("Player '{$player->displayName()}' milestone {$highestMilestone} already posted.");
            return;
        }

        Log::info("Player '{$player->displayName()}' reached {$highestMilestone} goals milestone!");

        try {
            $this->generateMilestoneStory($player, $highestMilestone, $totalGoals);
        } catch (\Exception $e) {
            Log::error("Error generating milestone story for player '{$player->displayName()}': " . $e->getMessage());
        }

        try {
            $this->generateMilestonePost($player, $highestMilestone, $totalGoals);
        } catch (\Exception $e) {
            Log::error("Error generating milestone post for player '{$player->displayName()}': " . $e->getMessage());
        }

        // Record that we've posted for this milestone
        IdempotencyRecord::record($idempotencyKey);
    }

    private function generateMilestoneStory(Player $player, int $milestone, int $totalGoals)
    {
        $imageManager = $this->getImageManager();

        // Create base canvas 1080x1920
        $image = $imageManager->canvas(self::STORY_WIDTH, self::STORY_HEIGHT, '#333333');

        // Get the stadium/playground image as background
        $backgroundPath = $this->getBackgroundImagePath($player);
        try {
            $background = $imageManager->make($backgroundPath);
            // Resize to cover the entire canvas while maintaining aspect ratio
            $background->fit(self::STORY_WIDTH, self::STORY_HEIGHT);
            $image->insert($background, 'center');
        } catch (\Exception $e) {
            Log::warning("Could not load background image for player '{$player->displayName()}': " . $e->getMessage());
        }

        // Overlay the template PNG (watermark and effects)
        $templatePath = public_path('social_media_poster/story_watermark.png');
        if (file_exists($templatePath)) {
            $template = $imageManager->make($templatePath);
            $template->fit(self::STORY_WIDTH, self::STORY_HEIGHT);
            $image->insert($template, 'center');
        }

        // Add player picture at center-top area
        $picturePath = $this->urlToFilePath($player->getAgeSafePicture());
        try {
            $picture = $imageManager->make($picturePath);
            $pictureSize = 750;
            $picture->fit($pictureSize, $pictureSize);

            // Make it circular by applying a mask
            $mask = $imageManager->canvas($pictureSize, $pictureSize, '#000000');
            $mask->circle($pictureSize - 2, $pictureSize / 2, $pictureSize / 2, function ($draw) {
                $draw->background('#ffffff');
            });
            $picture->mask($mask, false);

            $pictureX = (int) ((self::STORY_WIDTH - $pictureSize) / 2);
            $pictureY = (int) (self::STORY_HEIGHT * 0.09);
            $image->insert($picture, 'top-left', $pictureX, $pictureY);
        } catch (\Exception $e) {
            Log::warning("Could not load picture for player '{$player->displayName()}': " . $e->getMessage());
        }

        // Add player name below picture
        $playerNameY = (int) (self::STORY_HEIGHT * 0.53);
        $playerNameX = (int) (self::STORY_WIDTH / 2);

        // Shadow for player name
        $image->text($player->firstAndLastName(), $playerNameX - 4, $playerNameY + 5, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(90);
            $font->color('#000000');
            $font->align('center');
            $font->valign('center');
        });

        // Player name text
        $image->text($player->firstAndLastName(), $playerNameX, $playerNameY, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(90);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });

        // Add milestone number at center
        $milestoneY = (int) (self::STORY_HEIGHT * 0.70);
        $milestoneX = (int) (self::STORY_WIDTH / 2);

        // Shadow for milestone
        $image->text((string) $milestone, $milestoneX - 5, $milestoneY + 6, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(540);
            $font->color('#000000');
            $font->align('center');
            $font->valign('center');
        });

        // Milestone text
        $image->text((string) $milestone, $milestoneX, $milestoneY, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(540);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });

        // Add "GOLOS" text below milestone
        $golosY = (int) (self::STORY_HEIGHT * 0.87);

        $image->text('GOLOS', $milestoneX - 3, $golosY + 4, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(200);
            $font->color('#000000');
            $font->align('center');
            $font->valign('center');
        });

        $image->text('GOLOS', $milestoneX, $golosY, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(200);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });

        // Add club emblem in bottom-left corner (if player has a club)
        $club = $player->getClub();
        if ($club) {
            $emblemPath = $this->urlToFilePath($club->getEmblem());
            try {
                $emblem = $imageManager->make($emblemPath);
                $emblemSize = 150;
                $emblem->resize($emblemSize, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->insert($emblem, 'top-right', 40, 40);
            } catch (\Exception $e) {
                Log::warning("Could not load emblem for club '{$club->name}': " . $e->getMessage());
            }
        }

        // Ensure tmp directory exists
        $tmpDir = public_path(self::TEMP_FOLDER);
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        // Save the image
        $fileName = 'goal_milestone_story_' . $player->id . '_' . $milestone . '_' . now()->format('Y_m_d') . '.jpg';
        $absolutePath = public_path(self::TEMP_FOLDER . $fileName);
        $image->save($absolutePath, 90);

        $url = asset(self::TEMP_FOLDER . $fileName);
        Log::info("Generated goal milestone story image for player '{$player->displayName()}' at: $url");

        // Schedule the social media posts for 10AM Lisbon time
        $publishAt = Carbon::now('Europe/Lisbon')->setTime(7, 0, 0)->timezone('UTC');

        foreach (self::FB_AND_IG as $platform) {
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_STORY,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $url,
                'publish_at' => $publishAt->format('Y-m-d H:i:s'),
            ]);
        }

        Log::info("Scheduled goal milestone story posts for player '{$player->displayName()}'");
    }

    private function generateMilestonePost(Player $player, int $milestone, int $totalGoals)
    {
        $imageManager = $this->getImageManager();

        // Create base canvas 1000x1250
        $image = $imageManager->canvas(self::POST_WIDTH, self::POST_HEIGHT, '#333333');

        // Get the stadium/playground image as background
        $backgroundPath = $this->getBackgroundImagePath($player);
        try {
            $background = $imageManager->make($backgroundPath);
            // Resize to cover the entire canvas while maintaining aspect ratio
            $background->fit(self::POST_WIDTH, self::POST_HEIGHT);
            $image->insert($background, 'center');
        } catch (\Exception $e) {
            Log::warning("Could not load background image for player '{$player->displayName()}' (post): " . $e->getMessage());
        }

        // Overlay the template PNG (watermark and effects)
        $templatePath = public_path('social_media_poster/post_watermark.png');
        if (file_exists($templatePath)) {
            $template = $imageManager->make($templatePath);
            $template->fit(self::POST_WIDTH, self::POST_HEIGHT);
            $image->insert($template, 'center');
        }

        // Add player picture at center-top area
        $picturePath = $this->urlToFilePath($player->getAgeSafePicture());
        try {
            $picture = $imageManager->make($picturePath);
            $pictureSize = 500;
            $picture->fit($pictureSize, $pictureSize);

            // Make it circular by applying a mask
            $mask = $imageManager->canvas($pictureSize, $pictureSize, '#000000');
            $mask->circle($pictureSize - 2, $pictureSize / 2, $pictureSize / 2, function ($draw) {
                $draw->background('#ffffff');
            });
            $picture->mask($mask, false);

            $pictureX = (int) ((self::POST_WIDTH - $pictureSize) / 2);
            $pictureY = (int) (self::POST_HEIGHT * 0.08);
            $image->insert($picture, 'top-left', $pictureX, $pictureY);
        } catch (\Exception $e) {
            Log::warning("Could not load picture for player '{$player->displayName()}' (post): " . $e->getMessage());
        }

        // Add player name below picture
        $playerNameY = (int) (self::POST_HEIGHT * 0.50);
        $playerNameX = (int) (self::POST_WIDTH / 2);

        // Shadow for player name
        $image->text($player->firstAndLastName(), $playerNameX - 3, $playerNameY + 4, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(100);
            $font->color('#000000');
            $font->align('center');
            $font->valign('center');
        });

        // Player name text
        $image->text($player->firstAndLastName(), $playerNameX, $playerNameY, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(100);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });

        // Add milestone number at center
        $milestoneY = (int) (self::POST_HEIGHT * 0.70);
        $milestoneX = (int) (self::POST_WIDTH / 2);

        // Shadow for milestone
        $image->text((string) $milestone, $milestoneX - 4, $milestoneY + 5, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(490);
            $font->color('#000000');
            $font->align('center');
            $font->valign('center');
        });

        // Milestone text
        $image->text((string) $milestone, $milestoneX, $milestoneY, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(490);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });

        // Add "GOLOS" text below milestone
        $golosY = (int) (self::POST_HEIGHT * 0.90);

        $image->text('GOLOS', $milestoneX - 2, $golosY + 3, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(150);
            $font->color('#000000');
            $font->align('center');
            $font->valign('center');
        });

        $image->text('GOLOS', $milestoneX, $golosY, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(150);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });

        // Add club emblem in bottom-left corner (if player has a club)
        $club = $player->getClub();
        if ($club) {
            $emblemPath = $this->urlToFilePath($club->getEmblem());
            try {
                $emblem = $imageManager->make($emblemPath);
                $emblemSize = 120;
                $emblem->resize($emblemSize, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->insert($emblem, 'top-right', 30, 30);
            } catch (\Exception $e) {
                Log::warning("Could not load emblem for club '{$club->name}': " . $e->getMessage());
            }
        }

        // Ensure tmp directory exists
        $tmpDir = public_path(self::TEMP_FOLDER);
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
 
        // Save the image
        $fileName = 'goal_milestone_post_' . $player->id . '_' . $milestone . '_' . now()->format('Y_m_d') . '.jpg';
        $absolutePath = public_path(self::TEMP_FOLDER . $fileName);
        $image->save($absolutePath, 90);

        $url = asset(self::TEMP_FOLDER . $fileName);
        Log::info("Generated goal milestone post image for player '{$player->displayName()}' at: $url");

        // Schedule the social media posts for 10:05 AM Lisbon time (a few minutes after the story)
        $publishAt = Carbon::now('Europe/Lisbon')->setTime(10, 5, 0)->timezone('UTC');

        // Get player's club name for the post text
        $clubName = '';
        $club = $player->getClub();
        if ($club) {
            $clubName = " do {$club->name}";
        }

        foreach (self::FB_AND_IG as $platform) {
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_POST,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $url,
                'text_content' => "⚽ {$player->displayName()}{$clubName} atingiu a marca dos {$milestone} golos! Parabéns! 🎉",
                'publish_at' => $publishAt->format('Y-m-d H:i:s'),
            ]);
        }

        Log::info("Scheduled goal milestone post for player '{$player->displayName()}'");
    }
}
