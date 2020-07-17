<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePivotActivityUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_activity_user', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('activity_id')->unsigned();
                $table->foreign('activity_id')
                    ->references('id')
                    ->on('activity')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');

            $table->integer('user_id')->unsigned();
                $table->foreign('user_id')
                    ->references('id')
                    ->on('user')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');

            $table->string('status');
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
        Schema::drop('pivot_activity_user');
    }
}
