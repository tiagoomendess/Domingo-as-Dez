<?php

namespace App\Jobs;

use App\Notifications\ScoreReportBanNotification;
use App\ScoreReport;
use App\ScoreReportBan;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

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

                        $banKeys[] = $banKey;
                        $matchName = $scoreReport->game->home_team->club->name . " vs " . $scoreReport->game->away_team->club->name;
                        $reason = "Envio de um resultado falso no jogo $matchName";
                        ScoreReportBan::create([
                            'user_id' => $scoreReport->user_id,
                            'score_report_id' => $scoreReport->id,
                            'ip_address' => $scoreReport->ip_address,
                            'user_agent' => $scoreReport->user_agent,
                            'uuid' => $scoreReport->uuid,
                            'expires_at' => $banExpiration,
                            'reason' => $reason,
                        ]);

                        if (!empty($scoreReport->user)) {
                            $this->banUser($scoreReport->user, $reason, $banExpiration->format("d/m/Y \à\s H:i"));
                        } else {
                            Log::info("Report with no user associated. Skipping notification...");
                        }

                        Log::info("Banned a user: USER_ID:" . $scoreReport->user_id ?? '0' . ", IP: " . $scoreReport->ip_address . ", UUID: " . $scoreReport->uuid);
                    }
                }
            });

        $totalBans = count($banKeys);
        $endTime = new \DateTime();
        $diffTime = $endTime->diff($startTime);
        $elapsed = $diffTime->format("%I:%S");
        Log::info("ProcessScoreReportBans Job finished. $totalBans bans created out of $totalFakes fake scores and a total of $totalProcessed reports processed in $elapsed.");
    }

    private function banUser(User $user, string $reason, string $expiration) {
        try {
            Log::info("Notifying user " . $user->email . " about the ban...");
            $user->notify(
                new ScoreReportBanNotification(
                    $expiration,
                    $reason
                )
            );
        } catch (\Exception $e) {
            Log::error("Error banning user " . $user->email . ": " . $e->getMessage());
        }
    }
}
