<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('send_to')->unsigned();
            $table->foreign('send_to')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('reference_user_id')->unsigned()->nullable();
            $table->foreign('reference_user_id')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('reference_activity_id')->unsigned()->nullable();
            $table->foreign('reference_activity_id')
                ->references('id')
                ->on('activity')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('type');
            $table->string('message');
            $table->string('message_reference');
            $table->string('status');
            $table->string('error');

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
        Schema::drop('notification');
    }
}
