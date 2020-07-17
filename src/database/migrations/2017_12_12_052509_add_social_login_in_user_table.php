<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSocialLoginInUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function ($table) {
            $table->string('facebook_token')->after('current_longitude')->nullable();
            $table->string('twitter_token')->after('current_longitude')->nullable();
            $table->string('instagram_token')->after('current_longitude')->nullable();
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
            $table->dropColumn('facebook_token');
            $table->dropColumn('twitter_token');
            $table->dropColumn('instagram_token');
        });
    }
}
