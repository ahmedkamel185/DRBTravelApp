<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublishingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publishings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('status')->unsigned()->default(1);
            $table->enum('privacy',['public','private','flowers'])->default('public');
            $table->enum('type',['publisher','share'])->default('publisher');
            $table->bigInteger('publisher_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned();
            $table->bigInteger('sharer_id')->unsigned()->nullable();
            $table->foreign('publisher_id')->references('id')->on('publishers')->onDelete('cascade');
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->foreign('sharer_id')->references('id')->on('publishers')->onDelete('cascade');
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
        Schema::dropIfExists('publishings');
    }
}
