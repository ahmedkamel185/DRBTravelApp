<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagSpotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_spot', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("spot_id");
            $table->unsignedBigInteger("tag_id");

            $table->foreign('spot_id')->references('id')
                ->on('spots')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')
                ->on('tags')->onDelete('cascade');
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
        Schema::dropIfExists('tag_spot');
    }
}
