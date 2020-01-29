<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_resources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type',['image','vedio'])->default('image');
            $table->string('resource');
            $table->text("desc")->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('address')->nullable();
            $table->bigInteger('trip_id')->unsigned();
            $table->foreign('trip_id')->references('id')
                ->on('trips')->onDelete('cascade');
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
        Schema::dropIfExists('trip_resources');
    }
}
