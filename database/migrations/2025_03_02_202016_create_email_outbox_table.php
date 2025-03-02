<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailOutboxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_outbox', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('from', 155)->nullable();
            $table->text('to')->nullable();
            $table->text('cc')->nullable();
            $table->text('bcc')->nullable();
            $table->string('subject', 255)->nullable();
            $table->longText('body')->nullable();
            $table->text('headers')->nullable();
            $table->text('attachments')->nullable();
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
        Schema::dropIfExists('email_outbox');
    }
}
