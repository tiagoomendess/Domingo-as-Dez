<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollAnswerVoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_answer_votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('poll_answer_id');
            $table->foreign('poll_answer_id')->references('id')->on('poll_answers')->onDelete('cascade');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('ip', 70)->nullable();
            $table->index(['ip', 'user_id']);
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
        Schema::dropIfExists('poll_answer_votes');
    }
}
