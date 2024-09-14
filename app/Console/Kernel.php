<?php

namespace App\Console;

use App\Jobs\DeleteNotVerifiedAccounts;
use App\Jobs\GenerateGameComments;
use App\Jobs\GenerateGameImage;
use App\Jobs\ProcessDeleteRequest;
use App\Jobs\ProcessPolls;
use App\Jobs\ProcessScoreReportBans;
use App\Jobs\ScoreReportConsumer;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\Shit',
        'App\Console\Commands\GptInfo',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new ProcessDeleteRequest())->daily();
        $schedule->job(new DeleteNotVerifiedAccounts())->everyTenMinutes();
        $schedule->job(new GenerateGameImage())->everyMinute();
        $schedule->job(new ProcessPolls())->everyMinute();
        $schedule->job(new ProcessScoreReportBans())->dailyAt('22:59');
        $schedule->job(new ScoreReportConsumer())->everyMinute();
        $schedule->job(new GenerateGameComments())->everyMinute();
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
