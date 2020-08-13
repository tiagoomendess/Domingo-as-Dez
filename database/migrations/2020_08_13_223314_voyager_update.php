<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VoyagerUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('permissions', 'legacy_permissions');

        Schema::table('user_permissions', function (Blueprint $table) {
            $table->dropForeign('user_permissions_permission_id_foreign');
            $table->renameColumn('permission_id', 'legacy_permission_id');
            $table->foreign('legacy_permission_id')->references('id')->on('legacy_permissions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('legacy_permissions', 'permissions');

        Schema::table('user_permissions', function (Blueprint $table) {
            $table->dropForeign('user_permissions_legacy_permission_id_foreign');
            $table->renameColumn('legacy_permission_id', 'permission_id');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });


    }
}
