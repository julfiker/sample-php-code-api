<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdAsAssosiateduserToSportTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('sport', function ($table) {
            $table->integer('user_id')
                ->after('name')
                ->unsigned()
                ->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
        //
        Schema::table('sport', function ($table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
