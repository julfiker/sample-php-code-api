<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SportTableSeeder extends Seeder {

    public function run()
    {

        $sports = require(database_path('seeds/src/sports.php'));

        foreach($sports as $sport)
        {
            DB::table('sport')->insert([
                'name' => $sport,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]);
        }

    }

}