<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToActivityAndUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity', function ($table) {
            $table->string('title')->after('id');
            $table->string('country')->after('lat')->nullable();
            $table->string('city')->after('country')->nullable();
        });

        Schema::table('user', function ($table) {
            $table->double('current_latitude')->after('current_city')->nullable();
            $table->double('current_longitude')->after('current_latitude')->nullable();
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
            $table->dropColumn('title');
            $table->dropColumn('country');
            $table->dropColumn('city');
        });

        Schema::table('user', function ($table) {
            $table->dropColumn('current_latitude');
            $table->dropColumn('current_longitude');
        });
    }
}
