<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSportContactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sport_contact', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('self_id')->unsigned();
                $table->foreign('self_id')
                      ->references('id')
                      ->on('user')
                      ->onUpdate('cascade')
                      ->onDelete('cascade');

            $table->integer('sport_contact_id')->unsigned();
                $table->foreign('sport_contact_id')
                      ->references('id')
                      ->on('user')
                      ->onUpdate('cascade')
                      ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sport_contact');
    }
}
