<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddPerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert permissions
        DB::table('permissions')->insert([
            array(
                'name' => 'score_update',
            ),
            array(
                'name' => 'disable_ads',
            ),
            array(
                'name' => 'score-report-bans',
            ),
            array(
                'name' => 'score-report-bans.edit',
            ),
            array(
                'name' => 'score_report_bans.create',
            ),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
