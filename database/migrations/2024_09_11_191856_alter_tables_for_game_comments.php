<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablesForGameComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('contact_email', 155)->nullable()->after('name');
        });

        Schema::table('clubs', function (Blueprint $table) {
            $table->string('contact_email', 155)->nullable()->after('name');
            $table->unsignedInteger('admin_user_id')->after('contact_email')->nullable();
            $table->foreign('admin_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('contact_email');
        });

        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn('contact_email');
            $table->dropForeign('clubs_admin_user_id_foreign');
            $table->dropColumn('admin_user_id');
        });
    }
}
