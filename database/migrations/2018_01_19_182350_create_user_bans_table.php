<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('banned_user_id')->unsigned();
            $table->foreign('banned_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('reason');
            $table->integer('banned_by_user_id')->unsigned();
            $table->foreign('banned_by_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('pardoned')->default(false);
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
        Schema::dropIfExists('user_bans');
    }
}
