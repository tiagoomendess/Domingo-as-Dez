<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreReportBans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_report_bans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->string('uuid', 40)->nullable();
            $table->string('user_agent', 155)->nullable();
            $table->boolean('shadow_ban')->default(false);
            $table->boolean('ip_ban')->default(false);
            $table->string('reason', 255)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('ip_address');
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('score_report_bans');
    }
}
