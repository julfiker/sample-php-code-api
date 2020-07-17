<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusFieldIntoUserGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Status 0 = Request, 1 = Joined, 3 = Cancel
        Schema::table('user_group', function ($table) {
            $table->integer('status')->after('group_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('user_group', function ($table) {
            $table->dropColumn(['status']);
        });
    }
}
