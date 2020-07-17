<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCityCodeAddressInActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity', function ($table) {
            $table->string('country_code')->after('country')->nullable();
            $table->string('address')->after('city')->nullable();
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
            $table->dropColumn('country_code');
            $table->dropColumn('address');
        });
    }
}
