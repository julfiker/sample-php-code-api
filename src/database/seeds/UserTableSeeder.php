<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('user')->insert([
            'first_name' => 'Peter',
            'last_name' => 'Pan',
            'birthday' => '1979-12-14',
            'email' => 'peter.pan@spoly.com',
            'password' => bcrypt('supersecret')
        ]);

//        factory('App\\Models\\Eloquent\\User\\User', 50)->create();
    }

}