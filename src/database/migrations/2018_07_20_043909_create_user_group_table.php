<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Create a table for group entity
        Schema::create('user_group', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')
                ->unsigned()
                ->nullable();
            $table->integer('group_id')
                ->unsigned()
                ->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            ;

            $table->foreign('group_id')
                ->references('id')
                ->on('group')
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
        Schema::table('user_group', function ($table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropForeign(['group_id']);
            $table->dropColumn(['group_id']);
        });
        Schema::drop('user_group');
    }
}
