<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Shit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shit:shit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Starting shit");
        $users = User::all();

        foreach ($users as $user) {

            if (!empty($user->password)) {
                $p = $user->password;
                $this->info($user->password);
                $a = decrypt("{\"value\": $p}");
                dd($a);
            }

        }

        $this->info('Ending Shit');
    }
}
