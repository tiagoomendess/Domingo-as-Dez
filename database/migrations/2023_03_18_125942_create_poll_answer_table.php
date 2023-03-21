<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('poll_id');
            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
            $table->string('answer', 144);
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
        Schema::dropIfExists('poll_answers');
    }
}
