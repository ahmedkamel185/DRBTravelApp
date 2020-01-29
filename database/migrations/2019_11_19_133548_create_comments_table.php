<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text("comment");
            $table->unsignedBigInteger("publisher_id");
            $table->unsignedBigInteger("spot_id");

            $table->foreign('publisher_id')->references('id')
                ->on('publishers')->onDelete('cascade');
            $table->foreign('spot_id')->references('id')
                ->on('spots')->onDelete('cascade');
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
        Schema::dropIfExists('comments');
    }
}
