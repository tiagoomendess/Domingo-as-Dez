<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteNotVerifiedAccounts implements ShouldQueue
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
        $startTime = new \DateTime();
        Log::debug('Deleting not verified accounts older than 15 minutes');
        $from = Carbon::now()->subMinutes(15);

        try {
            $result = DB::table('users')
                ->whereNotNull('email')
                ->where([
                    ['verified', '=', 0],
                    ['created_at', '<', $from->format("Y-m-d H:i:s")]
                ])->update([
                    'name' => null,
                    'email' => null,
                    'password' => null,
                    'email_token' => null,
                    'remember_token' => null
                ]);

            Log::debug("$result accounts deleted!");
        } catch (\Exception $e) {
            Log::error("Error executing job. Message: " . $e->getMessage());
        }

        $endTime = new \DateTime();
        $diff = $endTime->diff($startTime);
        Log::debug("Job DeleteNotVerifiedAccounts finished in " . $diff->format('%s seconds %F microseconds'));
    }
}
