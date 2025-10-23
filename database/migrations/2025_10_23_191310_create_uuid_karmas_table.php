<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\UuidKarma;
use App\ScoreReportBan;
use App\ScoreReport;
use Illuminate\Support\Facades\DB;
use App\Game;

class CreateUuidKarmasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            // Start DB transaction
            DB::beginTransaction();

            Schema::create('uuid_karmas', function (Blueprint $table) {
                $table->string('uuid', 36)->primary();
                $table->integer('karma')->default(0);
                $table->timestamps();
            });

            // distinct uuids because some might be duplicated
            $uuids = ScoreReport::where('uuid', '!=', null)->distinct()->pluck('uuid');
            $uuids = $uuids->unique()->toArray();

            foreach ($uuids as $uuid) {
                $uuidKarma = new UuidKarma();
                $uuidKarma->uuid = $uuid;
                $uuidKarma->karma = 0;
                $uuidKarma->save();
            }

            // try and add karma to the uuids
            // Get all score reports that have the field finished set to 1
            // And then compare to the actual result of the game. If it matches, add 1 karma to the uuid
            // This is the only way we have to add karma to the uuids
            $scoreReports = ScoreReport::where('finished', '=', 1)->get();
            foreach ($scoreReports as $scoreReport) {
                $game =$scoreReport->game;

                $karmaToIncrease = 0;

                // If it reports the correct result, = 3 кarms+
                if ($game->getHomeScore() == $scoreReport->home_score && $game->getAwayScore() == $scoreReport->away_score) {
                   $karmaToIncrease += 3;
                }

                // If user sent location then +1 karma
                if ($scoreReport->isFromNearPlaygroundLocation()) {
                    $karmaToIncrease += 1;
                }

                // If user sent report while logged in then +1 karma
                if ($scoreReport->user_id != null) {
                    $karmaToIncrease += 1;
                }

                DB::update('UPDATE uuid_karmas SET karma = karma + ? WHERE uuid = ?', [$karmaToIncrease, $scoreReport->uuid]);
            }

            // Get all ScoreReportBans and decrease the karma of the uuids
            $scoreReportBans = ScoreReportBan::all();
            foreach ($scoreReportBans as $scoreReportBan) {
                DB::update('UPDATE uuid_karmas SET karma = karma - 3 WHERE uuid = ?', [$scoreReportBan->uuid]);
            }

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uuid_karmas');
    }
}
