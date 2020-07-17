<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeOtherFieldsIntoGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('group', function ($table) {
            $table->boolean('is_private')->after('user_id')->nullable();
            $table->boolean('is_disabled')->after('user_id')->nullable();
            $table->boolean('is_deleted')->after('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('group', function ($table) {
            $table->dropColumn(['is_private']);
            $table->dropColumn('is_disabled');
            $table->dropColumn('is_deleted');
        });
    }
}
