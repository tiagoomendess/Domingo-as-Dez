<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrioriryToPlaygrounds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('playgrounds', function (Blueprint $table) {
            $table->unsignedInteger('priority')->default(0)->after('visible');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('playgrounds', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
}
