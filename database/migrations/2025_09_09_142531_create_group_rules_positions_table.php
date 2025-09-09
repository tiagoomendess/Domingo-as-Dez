<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupRulesPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_rules_positions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('group_rules_id');
            $table->string('positions');
            $table->string('color');
            $table->string('label');
            $table->timestamps();
            $table->foreign('group_rules_id')->references('id')->on('group_rules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_rules_positions');
    }
}
