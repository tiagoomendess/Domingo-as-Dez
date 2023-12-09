<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationVerifiedColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('score_reports', function (Blueprint $table) {
            $table->point('location')->nullable()->after('user_agent');
            $table->decimal('location_accuracy', 8, 4)->nullable()->after('location');
            $table->string('uuid', 40)->nullable()->after('location_accuracy');
            $table->index('uuid');
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
            $table->dropColumn('location');
            $table->dropColumn('location_accuracy');
            $table->dropColumn('uuid');
        });
    }
}
