<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone', 20)->nullable();
            $table->text('bio')->nullable();
            $table->integer('user_id')->unique()->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('picture')->nullable();
            $table->timestamp('account_data_consent')->nullable();
            $table->timestamp('analytics_cookies_consent')->nullable();
            $table->timestamp('all_data_consent')->nullable();
            $table->string('timezone', 20)->default('Europe/Lisbon');
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
        Schema::dropIfExists('user_profiles');
    }
}
