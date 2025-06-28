<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_agents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('player_id')->nullable(); // A player can also be a team agent, rare but possible
            $table->foreign('player_id')->references('id')->on('players');
            $table->unsignedInteger('team_id')->nullable();
            $table->foreign('team_id')->references('id')->on('teams');
            $table->string('name');
            $table->string('birth_date')->nullable();
            $table->string('external_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('picture')->nullable();
            $table->enum('agent_type', ['manager', 'assistant_manager', 'goalkeeper_manager', 'director'])->default('manager');
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
        Schema::dropIfExists('team_agents');
    }
}
