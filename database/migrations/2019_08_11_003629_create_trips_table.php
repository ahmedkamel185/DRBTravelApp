<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('start_lat');
            $table->double('start_lng');
            $table->string('start_address');
            $table->double('end_lat');
            $table->double('end_lng');
            $table->string('end_address');
            $table->string('map_screen_shot')->default('default_image.png');
            $table->double('distance');
            $table->string('estimated_duration');
            $table->integer('status')->unsigned()->default(0);
            $table->timestamp('ended_at')->nullable();
            $table->text('desc')->nullable();
            $table->enum('privacy',['public','private','flowers'])->default('public');
            $table->bigInteger('publisher_id')->unsigned();
            $table->foreign('publisher_id')->references('id')->on('publishers')->onDelete('cascade');
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
        Schema::dropIfExists('trips');
    }
}
