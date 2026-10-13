<?php

namespace App\Jobs;

use App\Services\UploadStorage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CleanupTmpFolder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of days after which files are considered old.
     */
    const DAYS_TO_KEEP = 3;

    const TMP_FOLDER = '/storage/tmp';

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
        Log::info("Starting CleanupTmpFolder...");

        try {
            $this->doHandle();
        } catch (\Exception $e) {
            Log::error("Error cleaning up tmp folder: " . $e->getMessage());
        }

        $endTime = new \DateTime();
        $diffTime = $endTime->diff($startTime);
        $elapsed = $diffTime->format("%s seconds %F microseconds");
        Log::info("CleanupTmpFolder Job finished in $elapsed.");
    }

    private function doHandle()
    {
        $cutoff = Carbon::now()->subDays(self::DAYS_TO_KEEP)->getTimestamp();
        $deletedCount = 0;
        $skippedCount = 0;

        foreach (UploadStorage::files(self::TMP_FOLDER) as $url) {
            if (basename($url) === '.gitkeep') {
                continue;
            }

            if (UploadStorage::lastModified($url) < $cutoff) {
                UploadStorage::delete($url);
                $deletedCount++;
                Log::debug("Deleted old tmp file: $url");
            } else {
                $skippedCount++;
            }
        }

        Log::info("CleanupTmpFolder completed: deleted $deletedCount file(s), kept $skippedCount file(s).");
    }
}
