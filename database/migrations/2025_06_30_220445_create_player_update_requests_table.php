<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePlayerUpdateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_update_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('player_id')->nullable();
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            
            // Update data fields
            $table->string('name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('club_name')->nullable();
            $table->text('picture_url')->nullable();
            $table->string('association_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('facebook_profile')->nullable();
            $table->timestamp('birth_date')->nullable();
            $table->enum('position', ['none', 'striker', 'midfielder', 'defender', 'goalkeeper'])->nullable();
            $table->text('obs')->nullable();
            
            // Status and management fields
            $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');
            $table->string('created_by')->nullable(); // System/source that created the request
            $table->unsignedInteger('reviewed_by')->nullable(); // Admin user who reviewed it
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->text('source_data')->nullable(); // JSON data from external source
            
            $table->timestamps();
            
            // Indexes
            $table->index('player_id');
            $table->index('status');
            $table->index('created_by');
            $table->index('reviewed_by');
        });

        // Add permissions for the new feature
        DB::table('permissions')->insert([
            ['name' => 'player_update_requests'],
            ['name' => 'player_update_requests.edit'],
            ['name' => 'player_update_requests.create'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_update_requests');
    }
}
