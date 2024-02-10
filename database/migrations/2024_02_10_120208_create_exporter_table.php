<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExporterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_exports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 155);
            $table->string('model', 100);
            $table->string('fields', 300);
            $table->string('query', 500);
            $table->string('order_by', 100);
            $table->string('order_direction', 4);
            $table->string('format', 100)->default('csv');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
            $table->string('message', 300);
            $table->string('file_path', 300);
            $table->boolean('same_file')->default(false);
            $table->unsignedInteger('user_id')->references('id')->on('users');
            $table->timestamps();

            $table->index('name');
            $table->index('model');
            $table->index('status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_exports');
    }
}
