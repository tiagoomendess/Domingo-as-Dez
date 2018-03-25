<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameRefereesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_referees', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('game_id')->unsigned();
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');

            $table->integer('referee_id')->unsigned();
            $table->foreign('referee_id')->references('id')->on('referees')->onDelete('cascade');

            $table->integer('referee_type_id')->unsigned();
            $table->foreign('referee_type_id')->references('id')->on('referee_types')->onDelete('cascade');

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
        Schema::dropIfExists('game_referees');
    }
}
