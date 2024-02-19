<?php

namespace App\Jobs;

use App\Notifications\ScoreReportBanNotification;
use App\ScoreReport;
use App\ScoreReportBan;
use App\User;
use App\UserUuid;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessScoreReportBans implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        Log::info("Starting ProcessScoreReportBans...");

        try {
            $this->doHandle();
        } catch (\Exception $e) {
            Log::error("Error processing score reports: " . $e->getMessage());
        }

        $endTime = new \DateTime();
        $diffTime = $endTime->diff($startTime);
        $elapsed = $diffTime->format("%s seconds %F microseconds");
        Log::info("ProcessScoreReportBans Job finished in $elapsed.");
    }

    private function doHandle()
    {
        $startFrom = Carbon::now()->subHours(24);
        $now = Carbon::now();

        $fromStr = $startFrom->format("Y-m-d H:i:s");
        $toStr = $now->format("Y-m-d H:i:s");
        Log::info("Getting all score reports from $fromStr to $toStr");

        $totalProcessed = 0;
        $totalFakes = 0;
        $banExpiration = Carbon::now()->addDays(8);
        $banKeys = [];

        ScoreReport::where('created_at', '>', $startFrom->format("Y-m-d H:i:s"))
            ->where('source', '=', 'website')
            ->where('created_at', '<', $now->format("Y-m-d H:i:s"))
            ->chunk(10, function ($scoreReports) use (&$totalProcessed, &$totalFakes, &$banKeys, $banExpiration) {
                foreach ($scoreReports as $scoreReport) {
                    $totalProcessed++;

                    if (!$scoreReport->game->finished || $scoreReport->game->postponed) {
                        Log::info("Match " . $scoreReport->game->id . " not finished or postponed. Skipping...");
                        continue;
                    }

                    if ($scoreReport->home_score > $scoreReport->game->getHomeScore() || $scoreReport->away_score > $scoreReport->game->getAwayScore()) {
                        $totalFakes++;

                        $banKey = implode("_", [$scoreReport->user_id, $scoreReport->ip_address, $scoreReport->uuid]);

                        if (in_array($banKey, $banKeys)) {
                            Log::info("User already banned. Skipping...");
                            continue;
                        }

                        $user_id = $scoreReport->user_id;
                        if (empty($user_id)) {
                            $user_id = UserUuid::getLastKnownUserId($scoreReport->uuid);
                        }

                        $matchName = $scoreReport->game->home_team->club->name . " vs " . $scoreReport->game->away_team->club->name;
                        $reason = "Envio de um resultado falso no jogo $matchName";

                        try {
                            ScoreReportBan::create([
                                'user_id' => $user_id,
                                'score_report_id' => $scoreReport->id,
                                'ip_address' => Str::limit($scoreReport->ip_address, 40, ''),
                                'user_agent' => Str::limit($scoreReport->user_agent, 255, ''),
                                'uuid' => Str::limit($scoreReport->uuid, 36, ''),
                                'expires_at' => $banExpiration,
                                'reason' => Str::limit($reason, 255, ''),
                            ]);
                        } catch (\Exception $e) {
                            Log::error("Error creating score report $scoreReport->id ban: " . $e->getMessage());
                            continue;
                        }
                        $banKeys[] = $banKey;

                        if (!empty($user_id)) {
                            $this->banUser($user_id, $reason, $banExpiration->format("d/m/Y \à\s H:i"));
                        } else {
                            Log::info("Report with no user associated. Skipping notification...");
                        }

                        Log::info("Banned a user, ban key: $banKey");
                    }
                }
            });

        $totalBans = count($banKeys);
        Log::info("$totalFakes fake scores, $totalBans bans created out of a total of $totalProcessed reports processed");
    }

    private function banUser(int $userId, string $reason, string $expiration)
    {
        try {
            $user = User::where('id', $userId)->get();
            Log::info("Notifying user " . $user->email . " via email about the ban...");
            $user->notify(
                new ScoreReportBanNotification(
                    $expiration,
                    $reason
                )
            );
        } catch (\Exception $e) {
            Log::error("Error sending notification to banned user " . $user->email . ": " . $e->getMessage());
        }
    }
}
