<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('home_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->integer('away_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->integer('season_id')->references('id')->on('seasons')->onDelete('cascade');
            $table->integer('round');
            $table->timestamp('date');
            $table->integer('playground_id')->references('id')->on('playgrounds')->nullable();
            $table->integer('goals_home')->nullable();
            $table->integer('goals_away')->nullable();
            $table->boolean('finished')->default(false);
            $table->boolean('visible')->default(true);
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
        Schema::dropIfExists('games');
    }
}
