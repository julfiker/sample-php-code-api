<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HotspotCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = require(database_path('seeds/src/hotspot_categories.php'));

        foreach($categories as $category)
        {
            DB::table('hotspot_category')->insert([
                'name'  => $category,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
        }
    }
}
