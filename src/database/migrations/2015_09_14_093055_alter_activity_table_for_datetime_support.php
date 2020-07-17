<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterActivityTableForDatetimeSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity', function ($table) {
            $table->dateTime('start_time')->change();
            $table->dateTime('end_time')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity', function ($table) {
            $table->time('start_time')->change();
            $table->time('end_time')->change();
        });
    }
}
