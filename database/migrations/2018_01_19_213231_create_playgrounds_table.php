<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaygroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playgrounds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('club_id')->nullable();
            $table->string('name');
            $table->string('surface');
            $table->integer('width'); //In meters
            $table->integer('height');
            $table->integer('capacity');
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
        Schema::dropIfExists('playgrounds');
    }
}
