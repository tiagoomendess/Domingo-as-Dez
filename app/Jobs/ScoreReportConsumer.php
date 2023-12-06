<?php

namespace App\Jobs;

use App\Game;
use App\ScoreReport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ScoreReportConsumer implements ShouldQueue
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
        $games = Game::getLiveGames();
        if ($games->count() == 0) {
            return;
        }
    }

    private function process_game(Game $game)
    {
        $now = Carbon::now();
        $listenFrom = $now->subMinutes(10);

        $reports = ScoreReport::where('game_id', '=', $game->id)
            ->where('created_at', '>', $listenFrom->format("Y-m-d H:i:s"))
            ->orderBy('created_at', 'asc')
            ->get();

        if ($reports->count() == 0) {
            return;
        }

        // Current Score
        $currentHomeScore = $game->getHomeScore();
        $currentAwayScore = $game->getAwayScore();
        $groupedByScore = array(array());
        $competingScoresPoints["$currentHomeScore-$currentAwayScore"] = 1;

        // Group reports by score
        foreach ($reports as $report) {
            $scoreKey = $report->home_score . '-' . $report->away_score;

            if (!isset($competingScoresPoints[$scoreKey])) {
                $competingScoresPoints[$scoreKey] = 0;
            }

            $groupedByScore[$scoreKey][] = $report;
        }

        // For each score, process all the reports
        foreach ($groupedByScore as $scoreKey => $reports){
            $sources = [];
            $scorePoints = 1;
            foreach ($reports as $report) {
                if (!in_array($report->source, $sources)) {
                    $sources[] = $report->source;
                }

                if ($report->location != null) {
                    $scorePoints++;
                    if ($report->accuracy != null && $report->accuracy < 200.0)
                        $scorePoints++;

                }
            }
        }
    }

    // calculate distance using haversine method
    function haversineGreatCircleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
