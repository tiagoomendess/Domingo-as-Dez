<?php

namespace App\Console;

use App\Console\Commands\shit;
use App\Jobs\DeleteNotVerifiedAccounts;
use App\Jobs\GenerateGameImage;
use App\Jobs\ProcessDeleteRequest;
use App\Jobs\ProcessPolls;
use App\Jobs\ProcessScoreReportBans;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Symfony\Component\HttpKernel\Log\Logger;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\Shit'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->job(new ProcessDeleteRequest())->daily();
        $schedule->job(new DeleteNotVerifiedAccounts())->everyMinute();
        $schedule->job(new GenerateGameImage())->everyMinute();
        $schedule->job(new ProcessPolls())->everyMinute();
        $schedule->job(new ProcessScoreReportBans())->dailyAt('22:59');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
