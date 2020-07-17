<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotspotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotspot', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->integer('category_id');
            $table->double('lat');
            $table->double('long');
            $table->string('address');
            $table->string('city');
            $table->string('country');
            $table->string('country_code');
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
        Schema::drop('hotspot');
    }
}
