<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupTmpFolder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of days after which files are considered old.
     */
    const DAYS_TO_KEEP = 3;

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
        $tmpPath = storage_path('app/public/tmp');

        if (!is_dir($tmpPath)) {
            Log::info("Tmp folder does not exist: $tmpPath");
            return;
        }

        $cutoffDate = Carbon::now()->subDays(self::DAYS_TO_KEEP);
        $deletedCount = 0;
        $skippedCount = 0;

        $files = scandir($tmpPath);

        foreach ($files as $file) {
            // Skip special entries and .gitkeep
            if ($file === '.' || $file === '..' || $file === '.gitkeep') {
                continue;
            }

            $filePath = $tmpPath . DIRECTORY_SEPARATOR . $file;

            // Skip directories
            if (is_dir($filePath)) {
                continue;
            }

            $fileModifiedTime = Carbon::createFromTimestamp(filemtime($filePath));

            if ($fileModifiedTime->lt($cutoffDate)) {
                if (unlink($filePath)) {
                    $deletedCount++;
                    Log::debug("Deleted old tmp file: $file");
                } else {
                    Log::warning("Failed to delete tmp file: $file");
                }
            } else {
                $skippedCount++;
            }
        }

        Log::info("CleanupTmpFolder completed: deleted $deletedCount file(s), kept $skippedCount file(s).");
    }
}

