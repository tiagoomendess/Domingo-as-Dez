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
    public function handle(): void
    {
        Log::info('Deleting not verified accounts older than 10 minutes');
        $tenMinutesAgo = Carbon::now()->subMinutes(10);

        try {
            $result = DB::table('users')
                ->whereNotNull('email')
                ->where([
                    ['verified', '=', 0],
                    ['created_at', '<', $tenMinutesAgo->format("Y-m-d H:i:s")]
                ])->update([
                    'name' => null,
                    'email' => null,
                    'password' => null,
                    'email_token' => null,
                    'remember_token' => null
                ]);

            Log::info("$result accounts deleted!");
        } catch (\Exception $e) {
            Log::error("Error executing job. Message: " . $e->getMessage());
        }
    }
}
