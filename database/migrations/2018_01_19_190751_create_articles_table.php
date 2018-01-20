<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('media_type', ['none', 'image', 'video', 'youtube', 'download', 'other']);
            $table->text('media_link')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('text');
            $table->integer('user_id');
            $table->timestamp('date');
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
        Schema::dropIfExists('articles');
    }
}
