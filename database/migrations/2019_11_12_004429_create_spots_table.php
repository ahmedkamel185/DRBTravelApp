<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('publisher_id');
            $table->foreign('publisher_id')->references('id')
                ->on('publishers')->onDelete('cascade');
            $table->text('loaction')->nullable();
            $table->text("desc")->nullable();
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->unsignedBigInteger('journal_id')->nullable();
            $table->foreign('journal_id')->references('id')
                ->on('journals')->onDelete('cascade');
            $table->integer('status')->unsigned();
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
        Schema::dropIfExists('spots');
    }
}
