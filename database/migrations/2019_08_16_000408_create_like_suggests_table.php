<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLikeSuggestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('like_suggests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('suggest_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();

            $table->foreign('user_id')->references('id')
                ->on('publishers')->onDelete('cascade');

            $table->foreign('suggest_id')->references('id')
                ->on('suggests')->onDelete('cascade');

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
        Schema::dropIfExists('like_suggests');
    }
}
