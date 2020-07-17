<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Create a table for group entity
        Schema::create('group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('role')
                ->nullable();
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
        //Drop table while run down or in rollback
        Schema::drop('hotspot');
    }
}
