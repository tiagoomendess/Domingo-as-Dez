<?php

namespace App\Jobs;

use App\Game;
use App\Variable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\SocialMediaPost;
use App\Services\MatchImageGeneratorService;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Log;

class ScheduleSocialMedia implements ShouldQueue
{
    const VARIABLE_PREFIX = 'social_media.';

    const STORY_TODAY_HAS_GAME = 'social_media.story_today_has_game';
    const STORY_DIRECTIONS_IMG = 'social_media.story_directions_img';
    const STORY_LIVE_MATCHES_NOW = 'social_media.story_live_matches_now';
    const STORY_MATCH_MVP = 'social_media.story_match_mvp';
    const STORY_FLASH_INTERVIEW = 'social_media.story_flash_interview';
    const STORY_GAME_HISTORY = 'social_media.story_game_history';
    const STORY_LIVE_RESULTS = 'social_media.story_live_results';
    const STORY_NO_FAKE_RESULTS = 'social_media.story_no_fake_results';
    const STORY_SEND_VIDEOS = 'social_media.story_send_videos';

    const POST_TODAY_HAS_GAME = 'social_media.post_today_has_game';
    const POST_COLLABORATE = 'social_media.post_collaborate';
    const POST_LIVE_RESULTS = 'social_media.post_live_results';

    const FB_AND_IG = [SocialMediaPost::PLATFORM_FACEBOOK, SocialMediaPost::PLATFORM_INSTAGRAM];
    const TEMP_FOLDER = 'storage/tmp/';
    const AMOUNT_MATCHES_TO_PROMOTE = 5;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $vars;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $allVariables = Variable::where('name', 'like', self::VARIABLE_PREFIX . '%')->get();
        foreach ($allVariables as $variable) {
            $this->vars[$variable->name] = $variable->value;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Scheduling social media for today');

        $todayMatches = Game::where('date', '>=', now()->startOfDay())
            ->where('date', '<=', now()->endOfDay())
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // If no matches today, then we don't schedule any posts
        if (empty($todayMatches)) {
            Log::info('No matches today, skipping social media scheduling');
            return;
        }
        
        // Create story and post saying today we have games at 7AM in Europe/Lisbon time
        $todayAt7AM = Carbon::now('Europe/Lisbon')->setTime(7, 0, 0);
        $todayAt7AM->timezone('UTC');
        foreach (self::FB_AND_IG as $platform) {
            // Create story
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_STORY,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $this->vars[self::STORY_TODAY_HAS_GAME],
                'publish_at' => $todayAt7AM->format('Y-m-d H:i:s'),
            ]);

            // Create post
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_POST,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $this->vars[self::POST_TODAY_HAS_GAME],
                'text_content' => 'Hoje tem futebol popular, lista de jogos em 🔗 https://domingoasdez.com/hoje',
                'publish_at' => $todayAt7AM->format('Y-m-d H:i:s'),
            ]);
        }

        // If there are X or less matches today, we post a story promoting each one,
        // more than this we don't because it's too much spam, so we select X random matches to promote.
        if ($todayMatches->count() <= self::AMOUNT_MATCHES_TO_PROMOTE) {
            try {
                $this->promoteMatches($todayMatches, $todayAt7AM->copy()->addMinute());
            } catch (\Exception $e) {
                Log::error('Error promoting today matches', ['error' => $e->getMessage()]);
            }
        } else {
            // Promote random matches
            try {
            $this->promoteRandomMatches($todayMatches, $todayAt7AM->copy()->addMinute(), self::AMOUNT_MATCHES_TO_PROMOTE);
            } catch (\Exception $e) {
                Log::error('Error promoting random matches', ['error' => $e->getMessage()]);
            }
        }

        $firstMatchTime = Carbon::parse($todayMatches->first()->date);

        // 1h before first match start, create directions story
        $oneHourBeforeFirstMatch = $firstMatchTime->copy()->subHours(1);
        foreach (self::FB_AND_IG as $platform) {
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_STORY,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $this->vars[self::STORY_DIRECTIONS_IMG],
                'publish_at' => $oneHourBeforeFirstMatch->format('Y-m-d H:i:s'),
            ]);
        }

        // 45m before first match start, create game history story
        $fortyFiveMinutesBeforeFirstMatch = $firstMatchTime->copy()->subMinutes(45);
        foreach (self::FB_AND_IG as $platform) {
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_STORY,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $this->vars[self::STORY_GAME_HISTORY],
                'publish_at' => $fortyFiveMinutesBeforeFirstMatch->format('Y-m-d H:i:s'),
            ]);
        }

        // Create the live matches now story
        foreach (self::FB_AND_IG as $platform) {
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_STORY,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $this->vars[self::STORY_LIVE_MATCHES_NOW],
                'publish_at' => $firstMatchTime->format('Y-m-d H:i:s'),
            ]);
        }

        // 10 minutes into the match publish LIVE_RESULTS story and post
        $tenMinutesAfterFirstMatch = $firstMatchTime->copy()->addMinutes(10);
        foreach (self::FB_AND_IG as $platform) {
            // Create story
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_STORY,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $this->vars[self::STORY_LIVE_RESULTS],
                'publish_at' => $tenMinutesAfterFirstMatch->format('Y-m-d H:i:s'),
            ]);

            // Create post
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_POST,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $this->vars[self::POST_LIVE_RESULTS],
                'text_content' => 'Acompanha em 🔗 https://domingoasdez.com/direto',
                'publish_at' => $tenMinutesAfterFirstMatch->format('Y-m-d H:i:s'),
            ]);
        }

        // 15 minutes after first kick off, create the send videos story
        $fifteenMinutesAfterFirstMatch = $firstMatchTime->copy()->addMinutes(15);
        foreach (self::FB_AND_IG as $platform) {
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_STORY,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $this->vars[self::STORY_SEND_VIDEOS],
                'publish_at' => $fifteenMinutesAfterFirstMatch->format('Y-m-d H:i:s'),
            ]);
        }

        // 45 minutes after first kick off, create the collaborate post
        foreach (self::FB_AND_IG as $platform) {
            // Create POST
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_POST,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $this->vars[self::POST_COLLABORATE],
                'text_content' => '🔴📣 Colabora e envia o resultado do jogo que estás a ver pelo site',
                'publish_at' => $firstMatchTime->copy()->addMinutes(45)->format('Y-m-d H:i:s'),
            ]);
        }

        // 1h after the first match has started, create the no fake results story
        $oneHourAfterFirstMatch = $firstMatchTime->copy()->addHours(1);
        foreach (self::FB_AND_IG as $platform) {
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_STORY,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $this->vars[self::STORY_NO_FAKE_RESULTS],
                'publish_at' => $oneHourAfterFirstMatch->format('Y-m-d H:i:s'),
            ]);
        }

        // 2 hours after the first match has started, create the vote on MVP story
        $twoHoursAfterFirstMatch = $firstMatchTime->copy()->addHours(2);
        foreach (self::FB_AND_IG as $platform) {
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_STORY,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $this->vars[self::STORY_MATCH_MVP],
                'publish_at' => $twoHoursAfterFirstMatch->format('Y-m-d H:i:s'),
            ]);
        }

        // 2h15m after first match has started, create the flash interview story
        foreach (self::FB_AND_IG as $platform) {
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_STORY,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $this->vars[self::STORY_FLASH_INTERVIEW],
                'publish_at' => $firstMatchTime->copy()->addHours(2)->addMinutes(15)->format('Y-m-d H:i:s'),
            ]);
        }

        try {
        $this->competitionsUpdated($todayMatches, $firstMatchTime->copy()->addHours(3));
        } catch (\Exception $e) {
            Log::error('Error adding competitions update story', ['error' => $e->getMessage()]);
        }

        Log::info('Social media scheduled successfully');
    }

    private function promoteMatches(Collection $matches, Carbon $publishAt, int $interval = 10)
    {
        $imageGenerator = new MatchImageGeneratorService(new ImageManager());
        $timeToPublish = $publishAt->copy();
        
        // Ensure tmp directory exists
        $tmpDir = public_path(self::TEMP_FOLDER);
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        
        foreach ($matches as $match) {
            // Build paths correctly: relative path for URL, absolute path for saving
            $relativePath = self::TEMP_FOLDER . 'match_promotion_story_' . $match->id . '.jpg';
            $absolutePath = public_path($relativePath);

            try {
                $imageGenerator->generateStoryImage($match)->save($absolutePath);
            } catch (\Exception $e) {
                Log::error('Error generating story image for match ' . $match->id, ['error' => $e->getMessage()]);
                return;
            }

            // Use asset() for files in public folder, not url()
            $url = asset($relativePath);
            foreach (self::FB_AND_IG as $platform) {
                SocialMediaPost::create([
                    'platform' => $platform,
                    'post_type' => SocialMediaPost::POST_TYPE_STORY,
                    'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                    'media_path' => $url,
                    'publish_at' => $timeToPublish->format('Y-m-d H:i:s'),
                ]);
            }

            $timeToPublish->addMinutes($interval);
        }
    }

    private function competitionsUpdated(Collection $games, Carbon $publishAt)
    {
        $competitionIdsUsed = [];
        $uniqueCompetitions = [];
        foreach ($games as $game) {
            if (!in_array($game->game_group->season->competition->id, $competitionIdsUsed)) {
                $competitionIdsUsed[] = $game->game_group->season->competition->id;
                $uniqueCompetitions[] = $game->game_group->season->competition;
            }
        }

        foreach ($uniqueCompetitions as $competition) {
            if (!$competition->visible) {
                continue;
            }

            SocialMediaPost::create([
                'platform' => SocialMediaPost::PLATFORM_FACEBOOK,
                'post_type' => SocialMediaPost::POST_TYPE_POST,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_TEXT,
                'text_content' => 'Classificação da ' . $competition->name . ' atualizada ⤵️' . $competition->getPublicUrl(),
                'publish_at' => $publishAt->format('Y-m-d H:i:s'),
            ]);
        }
    }

    private function promoteRandomMatches(Collection $matches, Carbon $publishAt, int $amount) {
        $interval = 1;
        $remainingMatches = count($matches) - $amount;
        if ($remainingMatches < 1 || $amount > count($matches)) {
            return;
        }

        $matches = $matches->shuffle();
        $this->promoteMatches($matches->random($amount), $publishAt, $interval);

        // Generate more mathes in website image. Get the pre built iamge from the public folder and
        // add the number of matches on top of it.
        $imageManager = new ImageManager();
        $image = $imageManager->make('public/social_media_poster/story_more_matches.png');

        // Insert $remainingMatches number of matches on top of the image
        $textX = (int) ($image->width() / 2);
        $textY = (int) ($image->height() / 2) - 140;

        // artificial shaddow
        $image->text(" $remainingMatches ", $textX - 6, $textY + 10, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(660);
            $font->color('#000000');
            $font->align('center');
            $font->valign('center');
        });

        $image->text(" $remainingMatches ", $textX, $textY, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(650);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });
        
        $imageFileName = now()->format('Y_m_d') . '_more_matches_story.jpg';
        $image->save(public_path(self::TEMP_FOLDER . $imageFileName));
        $url = asset(self::TEMP_FOLDER . $imageFileName);

        $timeToPublish = $publishAt->copy()->addMinutes(($interval * $amount));
        foreach (self::FB_AND_IG as $platform) {
            SocialMediaPost::create([
                'platform' => $platform,
                'post_type' => SocialMediaPost::POST_TYPE_STORY,
                'post_content_type' => SocialMediaPost::POST_CONTENT_TYPE_IMAGE,
                'media_path' => $url,
                'publish_at' => $timeToPublish->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
