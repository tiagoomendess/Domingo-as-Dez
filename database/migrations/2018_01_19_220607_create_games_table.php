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
            $table->integer('home_team_id');
            $table->integer('away_team_id');
            $table->integer('season_id');
            $table->integer('round');
            $table->timestamp('date');
            $table->integer('playground_id')->nullable();
            $table->integer('goals_home')->nullable();
            $table->integer('goals_away')->nullable();
            $table->boolean('finished');
            $table->boolean('visible');
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
