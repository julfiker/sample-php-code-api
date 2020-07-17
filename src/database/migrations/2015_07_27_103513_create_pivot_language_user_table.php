<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePivotLanguageUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_language_user', function (Blueprint $table) {

            $table->integer('language_id')->unsigned();
                $table->foreign('language_id')
                    ->references('id')
                    ->on('language')
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
        Schema::drop('pivot_language_user');
    }
}
