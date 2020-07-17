<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            
            $table->increments('id');

            // Basic profile
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birthday');
            $table->string('email')->unique();
            $table->string('password', 60);

            // Extended profile
            $table->string('username')->unique()->nullable();
            $table->string('shirtname')->nullable();
            $table->string('gender')->nullable();
            $table->text('about_me')->nullable();
            $table->string('sportquote')->nullable();

            $table->string('cover_photo')->nullable();
            $table->string('profile_photo')->nullable();

            $table->string('birth_country')->nullable();
            $table->string('current_country')->nullable();
            $table->string('current_city')->nullable();

            // Pivot brand
            // Pivot sport
            // Pivot language

            // Extras
            $table->timestamps();
            $table->softDeletes();

        });

        DB::statement('ALTER TABLE user ADD FULLTEXT search_user(first_name, last_name, shirtname, current_city, current_country)');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        \Illuminate\Support\Facades\Schema::table('user', function($table) {

            $table->dropIndex('search_user');

        });

        Schema::drop('user');

    }
}
