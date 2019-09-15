<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('icon')->default('default_image.png');
            $table->timestamps();
        });
        $type   = new \App\Models\StoreType;
        $type->name_ar  = 'مطاعم';
        $type->name_en  = 'restaurnt';
        $type->save();

        $type   = new \App\Models\StoreType;
        $type->name_ar  = 'كافيه';
        $type->name_en  = 'coffee shop';
        $type->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_types');
    }
}
