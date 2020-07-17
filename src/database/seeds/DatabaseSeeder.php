<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(LanguageTableSeeder::class);
        $this->call(SportTableSeeder::class);
        $this->call(BrandTableSeeder::class);
        $this->call(NationalityTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(HotspotCategoryTableSeeder::class);

        Model::reguard();
    }
}
