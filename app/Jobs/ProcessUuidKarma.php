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
        $this->finishedScoreReportsMustMatchTheGameResult();
        $endTime = new \DateTime();
        $diffTime = $endTime->diff($startTime);
        $elapsed = $diffTime->format("%s seconds %F microseconds");
        Log::info("UUID Karma processed in $elapsed");
    }

    public function finishedScoreReportsMustMatchTheGameResult(): void
    {
        Log::info("Finished score reports must match the game result...");

        // Get all score reports in the last 24 hours that have finished set to 1
        $scoreReports = ScoreReport::where('finished', '=', 1)->where('created_at', '>', Carbon::now()->subHours(24))->get();
        foreach ($scoreReports as $scoreReport) {
            $game = $scoreReport->game;
            if ($game->getHomeScore() == $scoreReport->home_score && $game->getAwayScore() == $scoreReport->away_score) {
                $karmaUuid = $scoreReport->uuidKarma;
                if (empty($karmaUuid)) {
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
}
