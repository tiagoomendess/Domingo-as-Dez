<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->unique('slug');
            $table->string('title');
            $table->string('picture');
            $table->text('body');
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });

        DB::table('permissions')->insert([
            ['name' => 'pages'],
            ['name' => 'pages.edit'],
            ['name' => 'pages.create'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
