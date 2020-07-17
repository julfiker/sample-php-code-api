<?php

use Illuminate\Database\Seeder;

class NationalityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nationalities = require(database_path('seeds/src/nationalities.php'));

        foreach($nationalities as $language)
        {
            DB::table('nationality')->insert([
                'name' => $language,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]);
        }
    }
}
