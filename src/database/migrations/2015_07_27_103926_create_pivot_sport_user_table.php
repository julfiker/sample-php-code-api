<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePivotSportUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_sport_user', function (Blueprint $table) {

            $table->integer('sport_id')->unsigned();
                $table->foreign('sport_id')
                      ->references('id')
                      ->on('sport')
                      ->onUpdate('cascade')
                      ->onDelete('cascade');

            $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->unsigned()
                      ->references('id')
                      ->on('user')
                      ->onUpdate('cascade')
                      ->onDelete('cascade');

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
        Schema::drop('pivot_sport_user');
    }
}
