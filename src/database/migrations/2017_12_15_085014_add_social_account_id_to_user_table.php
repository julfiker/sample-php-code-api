<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSocialAccountIdToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function ($table) {
            $table->string('facebook_id')->after('google_token')->nullable();
            $table->string('google_id')->after('google_token')->nullable();
            $table->string('twitter_id')->after('google_token')->nullable();
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
            $table->dropColumn('facebook_id');
            $table->dropColumn('google_id');
            $table->dropColumn('twitter_id');
        });
    }
}
