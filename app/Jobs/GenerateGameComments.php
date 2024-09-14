<?php

namespace App\Jobs;

use App\Game;
use App\GameComment;
use App\Mail\GameCommentEmail;
use App\Team;
use Carbon\Carbon;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GenerateGameComments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Starting GenerateGameComments Job");
        $startTime = new DateTime();

        $created = $this->run();

        $endTime = new DateTime();
        $diff = $endTime->diff($startTime);
        $delta = $diff->format('%s seconds %F microseconds');
        Log::info("A total of $created games were processed in $delta");
    }

    private function run(): int
    {
        $now = Carbon::now('Europe/Lisbon');
        $minFrom = Carbon::now('Europe/Lisbon')->subDays(2);

        $gamesWithoutComments = Game::doesntHave('gameComments')
            ->where('date', '>', $minFrom)
            ->where('date', '<', $now)
            ->where('finished', true)
            ->where('postponed', false)
            ->where('visible', true)
            ->limit(10)
            ->get();

        Log::info("Got " . $gamesWithoutComments->count() . " games to comment");

        foreach ($gamesWithoutComments as $game) {
            $this->handleGame($game);
        }

        return $gamesWithoutComments->count();
    }

    private function handleGame(Game $game)
    {
        try {
            // Create comment for home team
            $this->createComment($game, $game->homeTeam);

            // Create comment for away team
            $this->createComment($game, $game->awayTeam);
        } catch (\Exception $e) {
            Log::error("Error processing game $game->id: " . $e->getMessage());
        }
    }

    private function createComment(Game $game, Team $team)
    {
        $uuid = Str::uuid();
        $pin = $this->generateRandomPinNumber(4);

        // End of day in Lisbon Time
        $lisbonTime = Carbon::now('Europe/Lisbon');

        // Set the time to the end of the day in Lisbon time (23:59:59)
        $endOfDayLisbon = $lisbonTime->endOfDay();

        // Convert the time to UTC
        $endOfDayUTC = $endOfDayLisbon->setTimezone('UTC');

        $gameComment = GameComment::create([
            'uuid' => $uuid,
            'pin' => $pin,
            'game_id' => $game->id,
            'team_id' => $team->id,
            'deadline' => $endOfDayUTC->format('Y-m-d H:i:s'),
            'content' => '',
        ]);

        $notification_email = $this->getNotificationEmail($team);
        if (empty($notification_email)) {
            Log::info("No notification email found for team $team->id");
            return;
        }

        $this->sendNotificationEmail($notification_email, $game, $gameComment, $team);
    }

    private function getNotificationEmail(Team $team)
    {
        $notification_email = $team->contact_email;
        if (empty($notification_email)) {
            $notification_email = $team->club->contact_email;
        }

        if (empty($notification_email) && !empty($team->club->admin_user)) {
            $notification_email = $team->club->adminUser->email;
        }

        return $notification_email;
    }

    private function generateRandomPinNumber(int $digits): string
    {
        $pin = '';
        for ($i = 0; $i < $digits; $i++) {
            $pin = $pin . mt_rand(0, 9);
        }

        return $pin;
    }

    private function sendNotificationEmail(string $email, Game $game, GameComment $gameComment, Team $team)
    {
        // Send email
        Log::info("Sending email to $email for game $game->id with uuid $gameComment->uuid and pin $gameComment->pin");

        try {
            Mail::to($email)
                ->send(new GameCommentEmail($game, $gameComment, $team));
        } catch (\Exception $e) {
            Log::error("Could not send email for game comment $game->id: " . $e->getMessage());
        }
    }
}
