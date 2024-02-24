<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFinishedColumnScore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('score_reports', function (Blueprint $table) {
            $table->boolean('finished')->after('uuid')->default(false);
            $table->boolean('is_fake')->after('finished')->default(false);
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
            $table->dropColumn('is_fake');
            $table->dropColumn('finished');
        });
    }
}
