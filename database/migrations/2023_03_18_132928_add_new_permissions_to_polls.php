<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddNewPermissionsToPolls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert([
            ['name' => 'polls'],
            ['name' => 'polls.edit'],
            ['name' => 'polls.create'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->where('name', 'polls')->delete();
        DB::table('permissions')->where('name', 'polls.edit')->delete();
        DB::table('permissions')->where('name', 'polls.create')->delete();
    }
}
