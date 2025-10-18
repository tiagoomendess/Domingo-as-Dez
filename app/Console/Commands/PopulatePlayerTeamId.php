<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Player;
use App\Transfer;

class PopulatePlayerTeamId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmp:player_team_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Temporary command just to populate the team_id for the current team of all players';

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
        // The problem: Before the current team of every player was inferred from the Transfers table. Now I added a new team_id
        // to the Player model, and I want to go to each player, query the Transfers table to get the latest transfer, and set
        // the team_id of that transfer, because that's the current team of the player.

        $this->info('Populating player team_id...');

        $players = Player::all();
        $this->info('Found ' . $players->count() . ' players');

        foreach ($players as $player) {
            $latestTransfer = Transfer::where('player_id', $player->id)->orderBy('date', 'desc')->first();

            $team_id = null;
            if (!empty($latestTransfer)) {
                $team_id = $latestTransfer->team_id;
            }

            $player->team_id = $team_id;
            $player->save();
        }

        $this->info('Player team_id populated successfully');
    }
}
