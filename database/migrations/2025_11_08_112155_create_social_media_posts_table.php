<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialMediaPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_media_posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('published')->default(false);
            $table->timestamp('publish_at');
            $table->enum('platform', ['facebook', 'instagram'])->default('facebook'); // facebook or instagram
            $table->enum('post_type', ['post', 'story'])->default('post'); // The type of post
            $table->enum('post_content_type', ['text', 'image', 'video']); // The type of content to add to the post
            $table->string('text_content', 512)->nullable(); // The text to add to the post
            $table->string('media_path', 512)->nullable(); // The path to the media file, can be external url or local storage path
            $table->string('error_message', 1024)->nullable(); // The error message if the post fails to be published
            $table->string('media_external_id', 128)->nullable(); // This will be the container_id, photo_id or video_id from meta
            $table->string('post_id', 128)->nullable(); // This will be the post_id from meta once it's published

            $table->timestamps();

            $table->index('published');
            $table->index('publish_at');
            $table->index('media_external_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sociual_media_posts');
    }
}
