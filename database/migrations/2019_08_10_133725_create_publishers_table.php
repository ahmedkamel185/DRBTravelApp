<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublishersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publishers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('display_name');
            $table->string('username');
            $table->string('image')->default('default_image.png');
            $table->string('mobile')->unique();
            $table->string('city');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('verified')->default('1');
            $table->text('bio')->nullable();
            $table->string('lang')->nullable();
            $table->string("device_id")->nullable();
            $table->string("device_type")->nullable();
            $table->integer('status')->default(1);

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
        Schema::dropIfExists('publishers');
    }
}
