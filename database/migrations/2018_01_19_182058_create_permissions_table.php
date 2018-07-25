<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        DB::table('permissions')->insert([

            ['name' => 'admin'],
            ['name' => 'dashboard'],
            ['name' => 'articles'],
            ['name' => 'articles.edit'],
            ['name' => 'clubs'],
            ['name' => 'clubs.edit'],
            ['name' => 'teams'],
            ['name' => 'teams.edit'],
            ['name' => 'players'],
            ['name' => 'players.edit'],
            ['name' => 'competitions'],
            ['name' => 'competitions.edit'],
            ['name' => 'seasons'],
            ['name' => 'seasons.edit'],
            ['name' => 'messages'],
            ['name' => 'messages.edit'],
            ['name' => 'media'],
            ['name' => 'media.edit'],
            ['name' => 'users'],
            ['name' => 'users.edit'],
            ['name' => 'transfers'],
            ['name' => 'transfers.edit'],
            ['name' => 'games'],
            ['name' => 'games.edit'],
            ['name' => 'goals'],
            ['name' => 'goals.edit'],
            ['name' => 'permissions'],
            ['name' => 'permissions.edit'],
            ['name' => 'referees'],
            ['name' => 'referees.edit'],
            ['name' => 'game_groups'],
            ['name' => 'game_groups.edit'],

        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
