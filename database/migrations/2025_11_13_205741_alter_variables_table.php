<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE variables MODIFY COLUMN name VARCHAR(64)');
        DB::statement('ALTER TABLE variables ADD PRIMARY KEY (name)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variables', function (Blueprint $table) {
            DB::statement('ALTER TABLE variables MODIFY COLUMN name VARCHAR(20)');
        });
    }
}
