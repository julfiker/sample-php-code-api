<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class LanguageTableSeeder extends Seeder {

    public function run()
    {

        $languages = require(database_path('seeds/src/languages.php'));

        foreach($languages as $language)
        {
            DB::table('language')->insert([
                'name' => $language,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]);
        }

    }

}