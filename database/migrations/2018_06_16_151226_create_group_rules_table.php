<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->integer('promotes')->default(0);
            $table->integer('relegates')->default(0);
            $table->enum('type', ['points', 'elimination', 'other']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_rules');
    }
}
