<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use App\ScoreReport;
use App\UuidKarma;
use Illuminate\Support\Facades\Log;

class ProcessUuidKarma implements ShouldQueue
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
        Log::info("Processing UUID Karma...");
        try {
            $this->finishedScoreReportsMustMatchTheGameResult();
            $this->notFinishedScoreReportsWithIsCorrectTrue();
            $this->removeKarmaFromScoreReportsWithIsFakeTrue();
        } catch (\Exception $e) {
            Log::error("Error processing UUID Karma: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
        
        $endTime = new \DateTime();
        $diffTime = $endTime->diff($startTime);
        $elapsed = $diffTime->format("%s seconds %F microseconds");
        Log::info("UUID Karma processed in $elapsed");
    }

    public function finishedScoreReportsMustMatchTheGameResult(): void
    {
        Log::info("Processing finished score reports must match the game result...");

        // Get all score reports in the last 24 hours that have finished set to 1
        $scoreReports = ScoreReport::where('finished', '=', 1)
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->get();

        Log::info("Found " . count($scoreReports) . " score reports to process");

        foreach ($scoreReports as $scoreReport) {
            $game = $scoreReport->game;
            if ($game->getHomeScore() == $scoreReport->home_score && $game->getAwayScore() == $scoreReport->away_score) {
                $karmaUuid = $scoreReport->uuidKarma;
                if (empty($karmaUuid)) {
                    Log::info("Creating new UuidKarma for " . $scoreReport->uuid);
                    $karmaUuid = new UuidKarma();
                    $karmaUuid->uuid = $scoreReport->uuid;
                    $karmaUuid->karma = 3;
                    $karmaUuid->save();
                } else {
                    $karmaUuid->addKarma(3);
                }
            }
        }
    }

    public function notFinishedScoreReportsWithIsCorrectTrue(): void
    {
        Log::info("Processing not finished score reports with is correct true...");

        // Get all score reports in the last 24 hours that have finished set to 1
        $scoreReports = ScoreReport::where('finished', '=', 0)
        ->where('created_at', '>', Carbon::now()->subHours(24))
        ->where('is_correct', '=', 1)
        ->get();

        Log::info("Found " . count($scoreReports) . " score reports to process");

        foreach ($scoreReports as $scoreReport) {
            if (empty($scoreReport->uuid)) {
                Log::warning("Score report $scoreReport->id has no uuid");
                continue;
            }

            $uuidKarma = UuidKarma::ensureExists($scoreReport->uuid);
            if (empty($uuidKarma)) {
                Log::warning("Could not ensure UuidKarma for " . $scoreReport->uuid);
                continue;
            }

            $uuidKarma->addKarma(1);
        }
    }

    // Remove -1 karma to score reports that are with is_fake set to 1
    public function removeKarmaFromScoreReportsWithIsFakeTrue(): void
    {
        Log::info("Removing karma from score reports with is fake true...");

        // Get all score reports in the last 24 hours that have is_fake set to 1
        $scoreReports = ScoreReport::where('is_fake', '=', 1)
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->get();

        Log::info("Found " . count($scoreReports) . " score reports to process");

        foreach ($scoreReports as $scoreReport) {
            $uuidKarma = UuidKarma::ensureExists($scoreReport->uuid);
            if (empty($uuidKarma)) {
                Log::warning("Could not ensure UuidKarma for " . $scoreReport->uuid);
                continue;
            }

            $uuidKarma->addKarma(-1);
        }
    }
}
