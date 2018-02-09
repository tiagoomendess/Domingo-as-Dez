<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->text('picture')->nullable();
            $table->string('association_id')->unique()->nullable();
            $table->string('nickname')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('facebook_profile')->nullable();
            $table->text('obs')->nullable();
            $table->enum('position', ['none', 'striker', 'midfielder', 'defender', 'goalkeeper'])->default('none');
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
        Schema::dropIfExists('players');
    }
}
