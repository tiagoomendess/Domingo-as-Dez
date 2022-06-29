<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfoReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 12)->unique();
            $table->integer('user_id', false, true)->nullable();
            $table->enum('status', \App\InfoReport::ALLOWED_STATUS);
            $table->string('content', 500);
            $table->string('source', 155);
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
        Schema::dropIfExists('info_reports');
    }
}
