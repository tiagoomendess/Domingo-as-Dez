<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamAgentsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_agents_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('team_agent_id');
            $table->foreign('team_agent_id')->references('id')->on('team_agents');
            $table->unsignedInteger('team_id')->nullable();
            $table->enum('agent_type', ['manager', 'assistant_manager', 'goalkeeper_manager', 'director'])->default('manager');
            $table->timestamp('started_at')->useCurrent();
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
        Schema::dropIfExists('team_agents_history');
    }
}
