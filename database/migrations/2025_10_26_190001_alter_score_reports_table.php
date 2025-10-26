<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterScoreReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('score_reports', function (Blueprint $table) {
            $table->boolean('is_correct')->default(false)->after('is_fake');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('score_reports', function (Blueprint $table) {
            $table->dropColumn('is_correct');
        });
    }
}
