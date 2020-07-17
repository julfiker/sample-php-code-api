<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;

class ListControllerTest extends TestCase
{

    use DatabaseMigrations;

    public function testGetAllSports()
    {
        $this->withoutMiddleware();

        factory(App\Models\Eloquent\Lists\Sport::class, 10)->create();

        $response = $this->getJson("/sports");
        $this->assertEquals(10, count($response->data));
    }

    public function testGetAllBrands()
    {
        $this->withoutMiddleware();

        factory(App\Models\Eloquent\Lists\Brand::class, 20)->create();

        $response = $this->getJson("/brands");
        $this->assertEquals(20, count($response->data));
    }

    public function testGetAllLanguages()
    {
        $this->withoutMiddleware();

        factory(App\Models\Eloquent\Lists\Language::class, 30)->create();

        $response = $this->getJson("/languages");
        $this->assertEquals(30, count($response->data));
    }

    public function testGetAllNationalities()
    {
        $this->withoutMiddleware();

        factory(App\Models\Eloquent\Lists\Nationality::class, 30)->create();

        $response = $this->getJson("/nationalities");
        $this->assertEquals(30, count($response->data));
    }
}
