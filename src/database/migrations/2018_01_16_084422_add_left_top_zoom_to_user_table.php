<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLeftTopZoomToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function ($table) {
            $table->double('left')->after('facebook_id')->nullable();
            $table->double('top')->after('left')->nullable();
            $table->double('zoom')->after('top')->nullable();
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
            $table->dropColumn('left');
            $table->dropColumn('top');
            $table->dropColumn('zoom');
        });
    }
}
