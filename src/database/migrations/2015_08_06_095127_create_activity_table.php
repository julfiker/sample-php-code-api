<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('owner_id')->unsigned();

                $table->foreign('owner_id')
                    ->references('id')
                    ->on('user')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');

            $table->integer('sport_id')->unsigned()->nullable();

                $table->foreign('sport_id')
                    ->references('id')
                    ->on('sport')
                    ->onUpdate('cascade')
                    ->onDelete('set null');

            $table->time('start_time');
            $table->time('end_time');
            $table->string('description');
            $table->string('recurring');
            $table->string('privacy');
            $table->tinyInteger('max_participants');
            $table->double('long');
            $table->double('lat');

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
        Schema::drop('activity');
    }
}
