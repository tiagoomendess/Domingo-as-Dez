<?php

namespace App\Jobs;

use App\Game;
use App\ScoreReport;
use Carbon\Carbon;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

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
        $startTime = new DateTime();
        /** @var Game $games */
        $games = Game::getLiveGames();
        if ($games->count() == 0) {
            return;
        }

        Log::info("Starting ScoreReportConsumer...");
        foreach ($games as $game) {
            try {
                $this->process_game($game);
            } catch (\Exception $e) {
                Log::error("Error running ScoreReportConsumer when processing game " . $game->id . ": " . $e->getMessage());
            }
        }

        $endTime = new DateTime();
        $diff = $endTime->diff($startTime);
        $delta = $diff->format('%i:%s');
        Log::info("ScoreReportConsumer finished processing " . $games->count() . " games in $delta");
    }

    private function process_game(Game $game)
    {
        Log::debug("Start processing game " . $game->id);
        if ($game->finished) {
            Log::debug("Game " . $game->id . " is finished, skipping");
            return;
        }

        $now = Carbon::now();
        $listenFrom = $now->subMinutes(10);

        $reports = ScoreReport::where('game_id', '=', $game->id)
            ->where('created_at', '>', $listenFrom->format("Y-m-d H:i:s"))
            ->orderBy('created_at', 'asc')
            ->get();

        // At least 2 reports are needed to process
        if ($reports->count() <= 1) {
            Log::debug("Not enough reports were found for game " . $game->id);
            return;
        }

        Log::debug(count($reports) . " reports were found for game " . $game->id);

        // Current Score
        $currentHomeScore = $game->getHomeScore();
        $currentAwayScore = $game->getAwayScore();
        $currentScoreKey  = "$currentHomeScore-$currentAwayScore";
        $groupedByScore = [];
        $competingScoresPoints = null;

        // Group reports by score
        foreach ($reports as $report) {
            $scoreKey = $report->home_score . '-' . $report->away_score;
            $groupedByScore[$scoreKey][] = $report;
        }

        $scoresLabels = [];
        foreach ($groupedByScore as $scoreKey => $reports) {
            $scoresLabels[] = "$scoreKey with " . count($reports) . " reports";
        }
        Log::debug("Got the following results: " . implode(", ", $scoresLabels));

        // For each score, process all the reports
        foreach ($groupedByScore as $scoreKey => $reports){
            Log::debug("Processing reports for score $scoreKey");
            $sources = [];
            $scorePoints = 0;
            foreach ($reports as $report) {
                $reportPoints = 1;
                Log::debug("Processing report " . $report->id . " with score $scoreKey");
                if (!in_array($report->source, $sources)) {
                    $sources[] = $report->source;
                }

                // If location is set then add 1 point
                if ($report->location != null) {
                    $reportPoints++;
                    Log::debug("Has Location, +1 point ($reportPoints points)");

                    // If the location is accurate then add 1 point
                    $accuracy = $report->location_accuracy != null ? (int) $report->location_accuracy : null;
                    if ($accuracy != null && $accuracy < 300) {
                        // If the location is up to 150m from the game location
                        $distance = $this->haversineGreatCircleDistance(
                            $report->getLatitude(), $report->getLongitude(),
                            $game->playground->getLatitude(), $game->playground->getLongitude());
                        $distance = round($distance, 2);
                        if ($distance >= 1.0 && $distance <= 150.0) {
                            $reportPoints++;
                            Log::debug("Distance ($distance) for game is inside range, +1 point ($reportPoints points)");
                        } else {
                            Log::debug("Distance ($distance) for game IS NOT inside range ($reportPoints points)");
                        }
                    } else {
                        Log::debug("Location is NOT accurate, is $accuracy but needs to be NOT NULL and less than 300. Skipping distance calculation");
                    }
                } else {
                    Log::debug("Does NOT have location");
                }

                // if user_id exists then add 1 point
                if (!empty($report->user_id)) {
                    $reportPoints++;
                    Log::debug("Has user_id, +1 point ($reportPoints points)");
                }
                
                // If the score report is the same as the current score then add 1 point
                if ($currentScoreKey == $scoreKey) {
                    $reportPoints++;
                    Log::debug("Report score is the same as current one, +1 point ($reportPoints points)");
                }

                $scorePoints += $reportPoints;
                Log::debug("Report " . $report->id . " responsible for $reportPoints points ($scorePoints total)");
            }

            $amountOfSources = count($sources);
            if ($amountOfSources == 2) {
                $scorePoints++;
                Log::debug("2 different sources agreed on score $scoreKey, +1 point ($scorePoints total)");
            } else if ($amountOfSources >= 3) {
                Log::debug("3 or more different sources agreed on score $scoreKey, +2 points ($scorePoints total)");
                $scorePoints += 2;
            }

            Log::debug("Added $scorePoints points for score $scoreKey from " . count($reports) . " reports");
            $competingScoresPoints[$scoreKey] = $scorePoints;
        }

        $amountOfCompetingScores = count($competingScoresPoints);
        $competingScoresDesc = [];
        foreach ($competingScoresPoints as $scoreKey => $scorePoints) {
            $competingScoresDesc[] = "$scoreKey($scorePoints)";
        }

        Log::debug("Got $amountOfCompetingScores competing scores " . implode(", ", $competingScoresDesc));
        if ($amountOfCompetingScores == 1) {
            if ($competingScoresPoints[$scoreKey] > 4) {
                Log::debug("Score ($scoreKey) had " . $competingScoresPoints[$scoreKey] . " points and will update score");
                $this->updateGameScore($game, $scoreKey);
            } else {
                Log::debug("Score ($scoreKey) only had " . $competingScoresPoints[$scoreKey] . " points and will NOT update score");
            }

            return;
        } else if ($amountOfCompetingScores > 4) {
            Log::debug("Got $amountOfCompetingScores competing scores, too risky to update score");
            return;
        }

        arsort($competingScoresPoints);
        $highestScoreKey = array_keys($competingScoresPoints)[0];
        $secondHighestScoreKey = array_keys($competingScoresPoints)[1];
        $lowestScoreKey = array_keys($competingScoresPoints)[count($competingScoresPoints) - 1];

        $highestScorePoints = (float) $competingScoresPoints[$highestScoreKey];
        $secondHighestScorePoints = (float) $competingScoresPoints[$secondHighestScoreKey];
        $lowestScorePoints = (float) $competingScoresPoints[$lowestScoreKey];
        if ($lowestScoreKey == $secondHighestScoreKey) {
            $lowestScorePoints = $secondHighestScorePoints;
        }

        $canUpdateScore = $highestScorePoints >= 4.0
            && ($highestScorePoints / 2.0) >= $secondHighestScorePoints
            && ($highestScorePoints / 3.0) > $lowestScorePoints;

        $gameId = $game->id;
        $finalStats = "1st: $highestScoreKey($highestScorePoints) | 2nd: $secondHighestScoreKey($secondHighestScorePoints) | lowest: $lowestScoreKey($lowestScorePoints)";
        if ($canUpdateScore) {
            Log::info("Game $gameId will update score to $highestScoreKey: $finalStats");
            $this->updateGameScore($game, $highestScoreKey);
        } else {
            Log::info("Game $gameId will NOT update score: $finalStats");
        }
    }

    function updateGameScore(Game $game, string $scoreKey) {
        $previousScore = $game->getHomeScore() . '-' . $game->getAwayScore();
        if ($previousScore == $scoreKey) {
            Log::info("Score for game " . $game->id . " is already $scoreKey, not updating");
            return;
        }

        $scores = explode('-', $scoreKey);
        $homeScore = $scores[0];
        $awayScore = $scores[1];

        $game->goals_home = $homeScore;
        $game->goals_away = $awayScore;
        $game->save();
        Log::info("Updated score for game " . $game->id . " from $previousScore to $scoreKey");
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
