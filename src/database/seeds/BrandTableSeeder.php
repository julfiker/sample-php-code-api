<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BrandTableSeeder extends Seeder {

    public function run()
    {

        $brands = require(database_path('seeds/src/brands.php'));

        foreach($brands as $brand)
        {
            DB::table('brand')->insert([
                'name' => $brand,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]);
        }

    }

}