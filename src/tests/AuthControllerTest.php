<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;

class AuthControllerTest extends TestCase
{

    use DatabaseMigrations;
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testLogin()
    {
        // no user, so 403
        $this->post('/auth/login', ['email' => 'admin@spoly.com', 'password' => 'superSecret'])
            ->seeStatusCode(Response::HTTP_FORBIDDEN);

        // register a new user
        $registeredUser = $this->postJson('/user', [
            'email' => 'admin@spoly.com',
            'password' => 'superSecret',
            "first_name" => "Peter",
            "last_name" => "Pan",
            "birthday" => "1979-12-14",
        ])->data;

        $response = $this->postJson(
            '/auth/login',
            ['email' => 'admin@spoly.com', 'password' => 'superSecret'],
            Response::HTTP_CREATED,
            ['Authorization']
        );

        $this->assertNotEmpty($response->headers['Authorization']);
        $this->assertEquals($registeredUser->id, $response->data->id);
        $this->assertEquals($registeredUser->first_name, $response->data->first_name);
        $this->assertEquals($registeredUser->last_name, $response->data->last_name);
        $this->assertEquals($registeredUser->birthday, $response->data->birthday);
        // making sure statistics data is returned
        $this->assertEquals(0, $response->data->statistics->total_activity_created);
        $this->assertEquals(0, $response->data->statistics->total_activity_joined);
    }
}
