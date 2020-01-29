<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFavouritsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favourits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('publishing_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();

            $table->foreign('user_id')->references('id')
                ->on('publishers')->onDelete('cascade');

            $table->foreign('publishing_id')->references('id')
                ->on('publishings')->onDelete('cascade');

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
        Schema::dropIfExists('favourits');
    }
}
