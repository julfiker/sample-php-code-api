<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNationalityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nationality', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('user', function ($table) {
            $table->integer('nationality_id')
                ->after('current_city')
                ->unsigned()
                ->nullable();
            $table->foreign('nationality_id')
                ->references('id')
                ->on('nationality')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function ($table) {
            $table->dropForeign(['nationality_id']);
            $table->dropColumn('nationality_id');
        });
        Schema::drop('nationality');
    }
}
