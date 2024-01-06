<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryToAudit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit', function (Blueprint $table) {
            $table->string('ip_country', 155)->nullable()->after('ip_address');
        });

        Schema::table('score_reports', function (Blueprint $table) {
            $table->string('ip_country', 155)->nullable()->after('ip_address');
        });

        DB::statement('ALTER table `user_uuids` ADD CONSTRAINT `user_uuids_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audit', function (Blueprint $table) {
            $table->dropColumn('ip_country');
        });

        Schema::table('score_reports', function (Blueprint $table) {
            $table->dropColumn('ip_country');
        });
    }
}
