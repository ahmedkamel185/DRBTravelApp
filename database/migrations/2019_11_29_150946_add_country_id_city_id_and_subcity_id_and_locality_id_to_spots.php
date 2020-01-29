<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryIdCityIdAndSubcityIdAndLocalityIdToSpots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spots', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->after('lng');
            $table->unsignedBigInteger('city_id')->after('country_id');
            $table->unsignedBigInteger('subcity_id')->after('city_id');
            $table->unsignedBigInteger('locality_id')->after('subcity_id');

            $table->foreign('country_id')->references('id')
                ->on('countries')->onDelete('cascade');

            $table->foreign('city_id')->references('id')
                ->on('cities')->onDelete('cascade');

            $table->foreign('subcity_id')->references('id')
                ->on('subcities')->onDelete('cascade');

            $table->foreign('locality_id')->references('id')
                ->on('localities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spots', function (Blueprint $table) {
            Schema::dropIfExists('country');
            Schema::dropIfExists('city');
            Schema::dropIfExists('subcity');
            Schema::dropIfExists('locality');
        });
    }
}
