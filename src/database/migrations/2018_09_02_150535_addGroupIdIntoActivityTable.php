<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupIdIntoActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity', function ($table) {
            $table->integer('group_id')
                ->after('hotspot_id')
                ->unsigned()
                ->nullable();

            $table->foreign('group_id')
                ->references('id')
                ->on('group')
                ->onUpdate('cascade')
            ;
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
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
}
