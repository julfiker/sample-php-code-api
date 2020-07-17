<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSportContactRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sport_contact_request', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('from');
                $table->foreign('from')
                    ->references('id')
                    ->on('user')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');

            $table->unsignedInteger('to');
                $table->foreign('to')
                    ->references('id')
                    ->on('user')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');

            $table->string('status');

            $table->softDeletes();
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
        Schema::drop('sport_contact_request');
    }
}
