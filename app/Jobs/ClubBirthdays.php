<?php

namespace App\Jobs;

use App\Club;
use App\SocialMediaPost;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

class ClubBirthdays implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const STORY_WIDTH = 1080;
    const STORY_HEIGHT = 1920;
    const POST_WIDTH = 1000;
    const POST_HEIGHT = 1250;
    const TEMP_FOLDER = 'storage/tmp/';
    const FB_AND_IG = [SocialMediaPost::PLATFORM_FACEBOOK, SocialMediaPost::PLATFORM_INSTAGRAM];

    /**
     * Convert a URL path (e.g. /storage/media/images/...) to an absolute file path
     */
    private function urlToFilePath(string $urlPath): string
    {
        // Remove leading slash and prepend public_path
        $relativePath = ltrim($urlPath, '/');
        return public_path($relativePath);
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
        Log::info("Starting ClubBirthdays...");

        try {
            $this->doHandle();
        } catch (\Exception $e) {
            Log::error("Error processing club birthdays: " . $e->getMessage());
        }

        $endTime = new \DateTime();
        $diffTime = $endTime->diff($startTime);
        $elapsed = $diffTime->format("%s seconds %F microseconds");
        Log::info("ClubBirthdays Job finished in $elapsed.");
    }

    private function doHandle()
    {
        $today = Carbon::now();
        $month = $today->month;
        $day = $today->day;

        Log::info("Checking for club birthdays on $day/$month...");

        // Get all clubs that have their founding_date on the same day and month as today
        // and have birthday_post_enabled set to true
        $clubs = Club::whereNotNull('founding_date')
            ->where('birthday_post_enabled', true)
            ->whereMonth('founding_date', $month)
            ->whereDay('founding_date', $day)
            ->get();

        Log::info("Found " . $clubs->count() . " club(s) with birthday today.");

        foreach ($clubs as $club) {
            $foundingYear = Carbon::parse($club->founding_date)->year;
            $age = $today->year - $foundingYear;
            Log::info("Club '{$club->name}' celebrates {$age} years today!");

            try {
                $this->generateBirthdayStory($club, $age);
            } catch (\Exception $e) {
                Log::error("Error generating birthday story for club '{$club->name}': " . $e->getMessage());
            }

            try {
                $this->generateBirthdayPost($club, $age);
            } catch (\Exception $e) {
                Log::error("Error generating birthday post for club '{$club->name}': " . $e->getMessage());
            }
        }
    }

    private function generateBirthdayStory(Club $club, int $age)
    {
        $imageManager = new ImageManager();

        // Create base canvas 1080x1920
        $image = $imageManager->canvas(self::STORY_WIDTH, self::STORY_HEIGHT, '#333333');

        // Get the first playground image as background
        $playground = $club->getFirstPlayground();
        if ($playground) {
            $backgroundPath = $this->urlToFilePath($playground->getPicture());
            try {
                $background = $imageManager->make($backgroundPath);
                // Resize to cover the entire canvas while maintaining aspect ratio
                $background->fit(self::STORY_WIDTH, self::STORY_HEIGHT);
                $image->insert($background, 'center');
            } catch (\Exception $e) {
                Log::warning("Could not load playground image for club '{$club->name}': " . $e->getMessage());
            }
        }

        // Overlay the birthday_story.png template
        $templatePath = public_path('social_media_poster/birthday_story.png');
        $template = $imageManager->make($templatePath);
        $image->insert($template, 'center');

        // Add club name below "PARABÉNS" - approximately 25% from top
        $clubNameY = (int) (self::STORY_HEIGHT * 0.23);
        $clubNameX = (int) (self::STORY_WIDTH / 2);

        // Shadow for club name
        $image->text($club->name, $clubNameX - 4, $clubNameY + 5, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(120);
            $font->color('#000000');
            $font->align('center');
            $font->valign('center');
        });

        // Club name text
        $image->text($club->name, $clubNameX, $clubNameY, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(120);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });

        // Add club emblem at center
        $emblemPath = $this->urlToFilePath($club->getEmblem());
        try {
            $emblem = $imageManager->make($emblemPath);
            $emblemSize = 750;
            $emblem->fit($emblemSize, $emblemSize);
            $emblemX = (int) ((self::STORY_WIDTH - $emblemSize) / 2);
            $emblemY = (int) ((self::STORY_HEIGHT - $emblemSize) / 2);
            $image->insert($emblem, 'top-left', $emblemX, $emblemY);
        } catch (\Exception $e) {
            Log::warning("Could not load emblem for club '{$club->name}': " . $e->getMessage());
        }

        // Add the number of years at approximately 75% from top
        $ageY = (int) (self::STORY_HEIGHT * 0.78);
        $ageX = (int) (self::STORY_WIDTH / 2);

        // Shadow for age
        $image->text((string) $age, $ageX - 5, $ageY + 6, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(250);
            $font->color('#000000');
            $font->align('center');
            $font->valign('center');
        });

        // Age text
        $image->text((string) $age, $ageX, $ageY, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(250);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });

        // Ensure tmp directory exists
        $tmpDir = public_path(self::TEMP_FOLDER);
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        // Save the image
        $fileName = 'birthday_story_' . $club->id . '_' . now()->format('Y_m_d') . '.jpg';
        $absolutePath = public_path(self::TEMP_FOLDER . $fileName);
        $image->save($absolutePath, 90);

        $url = asset(self::TEMP_FOLDER . $fileName);
        Log::info("Generated birthday story image for club '{$club->name}' at: $url");

        // Schedule the social media posts for 8AM Lisbon time
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

        Log::info("Scheduled birthday story posts for club '{$club->name}'");
    }

    private function generateBirthdayPost(Club $club, int $age)
    {
        $imageManager = new ImageManager();

        // Create base canvas 1000x1250
        $image = $imageManager->canvas(self::POST_WIDTH, self::POST_HEIGHT, '#333333');

        // Get the first playground image as background
        $playground = $club->getFirstPlayground();
        if ($playground) {
            $backgroundPath = $this->urlToFilePath($playground->getPicture());
            try {
                $background = $imageManager->make($backgroundPath);
                // Resize to cover the entire canvas while maintaining aspect ratio
                $background->fit(self::POST_WIDTH, self::POST_HEIGHT);
                $image->insert($background, 'center');
            } catch (\Exception $e) {
                Log::warning("Could not load playground image for club '{$club->name}' (post): " . $e->getMessage());
            }
        }

        // Add a new layer on top of the background
        // Fill with black and only 25% opacity to darken the background
        $image->fill('#000000', 0, 0, function ($draw) {
            $draw->opacity(0.25);
        });

        // Overlay the birthday_post.png template
        $templatePath = public_path('social_media_poster/birthday_post.png');
        $template = $imageManager->make($templatePath);
        $image->insert($template, 'center');

        // Add club name below "PARABÉNS" - approximately 20% from top
        $clubNameY = (int) (self::POST_HEIGHT * 0.22);
        $clubNameX = (int) (self::POST_WIDTH / 2);

        // Shadow for club name
        $image->text($club->name, $clubNameX - 3, $clubNameY + 4, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(100);
            $font->color('#000000');
            $font->align('center');
            $font->valign('center');
        });

        // Club name text
        $image->text($club->name, $clubNameX, $clubNameY, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(100);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });

        // Add club emblem at center
        $emblemPath = $this->urlToFilePath($club->getEmblem());
        try {
            $emblem = $imageManager->make($emblemPath);
            $emblemSize = 500;
            $emblem->fit($emblemSize, $emblemSize);
            $emblemX = (int) ((self::POST_WIDTH - $emblemSize) / 2);
            $emblemY = (int) ((self::POST_HEIGHT - $emblemSize) / 2);
            $image->insert($emblem, 'top-left', $emblemX, $emblemY);
        } catch (\Exception $e) {
            Log::warning("Could not load emblem for club '{$club->name}' (post): " . $e->getMessage());
        }

        // Add the number of years at approximately 80% from top
        $ageY = (int) (self::POST_HEIGHT * 0.80);
        $ageX = (int) (self::POST_WIDTH / 2);

        // Shadow for age
        $image->text((string) $age, $ageX - 4, $ageY + 5, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(180);
            $font->color('#000000');
            $font->align('center');
            $font->valign('center');
        });

        // Age text
        $image->text((string) $age, $ageX, $ageY, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(180);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });

        // Ensure tmp directory exists
        $tmpDir = public_path(self::TEMP_FOLDER);
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        // Save the image
        $fileName = 'birthday_post_' . $club->id . '_' . now()->format('Y_m_d') . '.jpg';
        $absolutePath = public_path(self::TEMP_FOLDER . $fileName);
        $image->save($absolutePath, 90);

        $url = asset(self::TEMP_FOLDER . $fileName);
        Log::info("Generated birthday post image for club '{$club->name}' at: $url");

        // Schedule the social media posts for 8AM Lisbon time (a few minutes after the story)
        $publishAt = Carbon::now('Europe/Lisbon')->setTime(7, 5, 0)->timezone('UTC');

        foreach (self::FB_AND_IG as $platform) {
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_POST,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $url,
                'text_content' => "Parabéns {$club->name}! 🎂🎉 Hoje celebram {$age} anos de história!",
                'publish_at' => $publishAt->format('Y-m-d H:i:s'),
            ]);
        }

        Log::info("Scheduled birthday post for club '{$club->name}'");
    }
}
