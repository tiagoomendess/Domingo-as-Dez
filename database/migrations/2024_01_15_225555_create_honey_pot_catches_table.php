<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHoneyPotCatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('honey_pot_catches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ip_address', 39);
            $table->string('ip_country', 2)->nullable();
            $table->string('user_agent', 255);
            $table->string('route', 155);
            $table->string('http_method', 10);
            $table->string('query_params', 500);
            $table->text('headers');
            $table->text('cookies');
            $table->timestamps();

            $table->index('ip_address');
            $table->index('ip_country');
            $table->index('route');
            $table->index('http_method');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('honey_pot_catches');
    }
}
