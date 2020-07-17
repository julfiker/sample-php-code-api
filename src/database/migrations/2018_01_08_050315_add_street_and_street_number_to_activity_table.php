<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStreetAndStreetNumberToActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity', function ($table) {
            $table->string('street')->after('long')->nullable();
            $table->string('street_number')->after('street')->nullable();
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
            $table->dropColumn('street');
            $table->dropColumn('street_number');
        });
    }
}
