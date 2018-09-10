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
            $table->integer('club_id')->nullable()->unsigned();
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->string('name');
            $table->string('surface');
            $table->integer('width')->nullable(); //pitch In meters
            $table->integer('height')->nullable();
            $table->integer('capacity')->nullable();
            $table->text('picture')->nullable(); //Url to picture
            $table->text('obs')->nullable();
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();

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
