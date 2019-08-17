<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('store_name')->unique();
            $table->string('image')->default('default_image.png');
            $table->string('mobile')->unique();
            $table->string('city');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('address');
            $table->string("temporay_password")->nullable();
            $table->string('device_id')->nullable();
            $table->string('device_type')->nullable();
            $table->integer('status')->default(1);
            $table->integer('verified')->default(1);
            $table->string('lang')->nullable();
            $table->bigInteger('store_type_id')->unsigned();
            $table->foreign('store_type_id')->references('id')->on('store_types')->onDelete('cascade');
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
        Schema::dropIfExists('stores');
    }
}
