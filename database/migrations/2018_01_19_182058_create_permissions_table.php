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
            ['name' => 'articles.create'],
            ['name' => 'clubs'],
            ['name' => 'clubs.edit'],
            ['name' => 'clubs.create'],
            ['name' => 'teams'],
            ['name' => 'teams.edit'],
            ['name' => 'teams.create'],
            ['name' => 'players'],
            ['name' => 'players.edit'],
            ['name' => 'players.create'],
            ['name' => 'competitions'],
            ['name' => 'competitions.edit'],
            ['name' => 'competitions.create'],
            ['name' => 'seasons'],
            ['name' => 'seasons.edit'],
            ['name' => 'seasons.create'],
            ['name' => 'media'],
            ['name' => 'media.edit'],
            ['name' => 'media.create'],
            ['name' => 'users'],
            ['name' => 'users.edit'],
            ['name' => 'users.create'],
            ['name' => 'transfers'],
            ['name' => 'transfers.edit'],
            ['name' => 'transfers.create'],
            ['name' => 'games'],
            ['name' => 'games.edit'],
            ['name' => 'games.create'],
            ['name' => 'goals'],
            ['name' => 'goals.edit'],
            ['name' => 'goals.create'],
            ['name' => 'permissions'],
            ['name' => 'permissions.edit'],
            ['name' => 'permissions.create'],
            ['name' => 'referees'],
            ['name' => 'referees.edit'],
            ['name' => 'referees.create'],
            ['name' => 'game_groups'],
            ['name' => 'game_groups.edit'],
            ['name' => 'game_groups.create'],

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
