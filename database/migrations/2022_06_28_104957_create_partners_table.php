<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('url', 150);
            $table->string('picture', 255);
            $table->integer('priority', false, true);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });

        DB::table('permissions')->insert([
            ['name' => 'partners'],
            ['name' => 'partners.edit'],
            ['name' => 'partners.create'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partners');
    }
}
