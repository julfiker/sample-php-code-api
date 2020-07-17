<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test new user registration
     *
     * @return void
     */
    public function testNewUserRegistration()
    {
        // register a new user
        $this->post('/user', [
            'email' => 'admin@spoly.com',
            'password' => 'superSecret',
            'first_name' => 'köö',
            'last_name' => 'käö',
            'birthday' => '1979-12-14',
        ])
            ->seeStatusCode(Response::HTTP_CREATED)
            ->seeJson([
                    'id' => 1,
                    'first_name' => 'Köö',
                    'last_name' => 'Käö',
                ]
            )
            ->see('"data":{')
            ->dontSee('email')
            ->dontSee('password');
    }


    public function testValidateAgeInUserRegistration()
    {
        $minAgeLessThan13 = Carbon::now()->subYear(13)->startOfDay()->toDateString();

        // register a new user
        $this->post('/user', [
            'email' => 'admin@spoly.com',
            'password' => 'superSecret',
            'first_name' => 'köö',
            'last_name' => 'käö',
            'birthday' => $minAgeLessThan13,
        ])
            ->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->see('Sorry. Legally you need to be at least 13 years old to use Spoly.')
            ;

        $minAge13 = Carbon::now()->subYear(13)->subDay(1)->startOfDay()->toDateString();
        $this->post('/user', [
            'email' => 'admin@spoly.com',
            'password' => 'superSecret',
            'first_name' => 'köö',
            'last_name' => 'käö',
            'birthday' => $minAge13,
        ])
            ->seeStatusCode(Response::HTTP_CREATED)
            ->seeJson([
                    'id' => 1,
                    'first_name' => 'Köö',
                    'last_name' => 'Käö',
                ]
            )
            ->see('"data":{')
            ->dontSee('email')
            ->dontSee('password');
    }


    public function testViewUser()
    {
        // disable authentication
        $this->withoutMiddleware();

        $sport = factory(App\Models\Eloquent\Lists\Sport::class)->create();

        $mainUser = factory(App\Models\Eloquent\User\User::class)->create([
            'email' => 'admin@spoly.com',
            'password' => 'superSecret',
            'first_name' => 'Peter',
            'last_name' => 'Pan',
            'birthday' => '1979-12-14',
        ]);

        $secondUser = factory(App\Models\Eloquent\User\User::class)->create();

        $this->actingAs($mainUser)
            ->get('/user/' . $mainUser->id)
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson([
                    'id' => $mainUser->id,
                    'first_name' => 'Peter',
                    'last_name' => 'Pan',
                    'profile_photo' => 'http://localhost/files/image/profile_photo/' . $mainUser->id,
                    'cover_photo' => 'http://localhost/files/image/cover_photo/' . $mainUser->id,
                    'statistics' => [
                        'total_activity_created' => 0,
                        'total_activity_joined'  => 0,
                    ]
                ]
            )
            ->see('"data":{')
            ->dontSee('email')
            ->dontSee('password');

        // test user's statistics
        $this->_testUserStatistics($mainUser, $secondUser, $sport);

        // test relationship status
        $this->_testRelationshipStatus($mainUser, $secondUser);
    }

    private function _testUserStatistics($mainUser, $secondUser, $sport){
        // create an activity ================================
        $this->postJson('/activities', [
            'title' => 'title',
            'owner_id' => $mainUser->id,
            'sport_id' => $sport->id,
            'start_time' => '2005-10-01T12:00:00+00:00',
            'end_time' => '2005-10-01T14:00:00+00:00',
            'description' => 'description',
            'recurring' => 'no',
            'privacy' => 'open',
            'max_participants' => 3,
            'lat' => 88.123456, // (+-90)
            'long' => -111.123456, //(+-180)
        ]);

        $json = $this->getJson('/user/' . $mainUser->id);
        $this->assertEquals(1, $json->data->statistics->total_activity_created);
        $this->assertEquals(1, $json->data->statistics->total_activity_joined);

        // join to another activity =============================
        $this->actingAs($secondUser);
        // another activity by another user
        $activity = $this->postJson('/activities', [
            'title' => 'title',
            'owner_id' => $secondUser->id,
            'sport_id' => $sport->id,
            'start_time' => '2005-10-01T12:00:00+00:00',
            'end_time' => '2005-10-01T14:00:00+00:00',
            'description' => 'description',
            'recurring' => 'no',
            'privacy' => 'open',
            'max_participants' => 3,
            'lat' => 88.123456, // (+-90)
            'long' => -111.123456, //(+-180)
        ])->data;

        $this->put("/activities/{$activity->id}/invite", [
            'users' => [$mainUser->id],
        ])->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->actingAs($mainUser);
        $this->put("/activities/{$activity->id}/join")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $json = $this->getJson('/user/' . $mainUser->id);
        $this->assertEquals(1, $json->data->statistics->total_activity_created);
        $this->assertEquals(2, $json->data->statistics->total_activity_joined);
    }


    private function _testRelationshipStatus($mainUser, $secondUser) {
        $this->actingAs($mainUser);

        $json = $this->getJson('/user/' . $secondUser->id);
        $this->assertEquals('not_connected', $json->data->relationship_status);

        $connectedUser = factory(App\Models\Eloquent\User\User::class)->create();
        $this->sendInvitation($mainUser, $connectedUser)
            ->seeStatusCode(Response::HTTP_CREATED);
        $this->acceptRequest($mainUser, $connectedUser);

        $this->actingAs($mainUser);
        $json = $this->getJson('/user/' . $connectedUser->id);
        $this->assertEquals('connected', $json->data->relationship_status);

        $pendingRequestUser = factory(App\Models\Eloquent\User\User::class)->create();
        $this->sendInvitation($mainUser, $pendingRequestUser)
            ->seeStatusCode(Response::HTTP_CREATED);

        $this->actingAs($mainUser);
        $json = $this->getJson('/user/' . $pendingRequestUser->id);
        $this->assertEquals('pending', $json->data->relationship_status);
    }


    public function testSearchUser()
    {

        // disable authentication
        $this->withoutMiddleware();

        $loggedInUser = factory(App\Models\Eloquent\User\User::class)->create([
            'first_name' => 'Peter',
            'last_name'  => 'Pan',
        ]);
        $this->actingAs($loggedInUser);

        $peterPan = factory(App\Models\Eloquent\User\User::class)->create([
            'first_name' => 'Peter',
            'last_name'  => 'Pan',
        ]);

        $johnDoe = factory(App\Models\Eloquent\User\User::class)->create([
            'first_name' => 'John',
            'last_name'  => 'Doe',
        ]);

        $johnRoe = factory(App\Models\Eloquent\User\User::class)->create([
            'first_name' => 'John',
            'last_name'  => 'Roe',
        ]);

        $johnOliver = factory(App\Models\Eloquent\User\User::class)->create([
            'first_name' => 'John',
            'last_name'  => 'Oliver',
        ]);


        // Case 1: No one is friend with others ========================================

        $this->get('/user/search/pe')
            ->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        $json = $this->getJson('/user/search/peter');
        $this->assertEquals(1, count($json->data)); //logged in user will be skipped

        $json = $this->getJson('/user/search/John');
        $this->assertEquals(3, count($json->data));

        $json = $this->getJson('/user/search/Doe');
        $this->assertEquals(1, count($json->data));

        $json = $this->getJson('/user/search/John+Doe');
        $this->assertEquals(1, count($json->data));


        // Case 2: loggedInUser is became friend with JohnDoe ===========================

        $this->sendInvitation($loggedInUser, $johnDoe)
            ->seeStatusCode(Response::HTTP_CREATED);
        $this->acceptRequest($loggedInUser, $johnDoe);

        $json = $this->getJson('/user/search/John');
        $this->assertEquals(2, count($json->data)); // in previous case the count was 3


        // Case 3: johnRoe became friend with loggedInUser ==============================

        $this->sendInvitation($johnRoe, $loggedInUser)
            ->seeStatusCode(Response::HTTP_CREATED);
        $this->acceptRequest($johnRoe, $loggedInUser);

        $json = $this->getJson('/user/search/John');
        $this->assertEquals(1, count($json->data)); // in previous case the count was 2
    }

    public function testUpdateUser()
    {
        //TODO: Add more cases
        // disable authentication
        $this->withoutMiddleware();

        $nationality = factory(App\Models\Eloquent\Lists\Nationality::class)->create([
            'id' => 1,
            'name' => 'Finnish'
        ]);


        $brands = factory(App\Models\Eloquent\Lists\Brand::class, 20)->create();
        $sports = factory(App\Models\Eloquent\Lists\Sport::class, 20)->create();
        $languages = factory(App\Models\Eloquent\Lists\Language::class, 20)->create();


        $user = factory(App\Models\Eloquent\User\User::class)->create([
            'id' => 1,
            'email' => 'admin@spoly.com',
            'password' => 'superSecret',
            'first_name' => 'Peter',
            'last_name' => 'Pan',
            'birthday' => '1979-12-14',
        ]);

        $this->actingAs($user);

        $this->put('/user', [
            'first_name' => 'Köö',
            'last_name' => 'Käö',
            'shirtname' => 'shirtname ö',
            'birthday' => '1980-12-12',
            'gender' => 'male',
            'about_me' => 'about me ö',
            'sportquote'=> 'sport quote ö',
            'current_city' => 'current_city ö',
            'current_country' => 'current_country ö',
            'current_country_code' => 'FI',
            'birth_country' => 'birth_country ö',
            'nationality_id' => $nationality->id,
            'current_latitude' => 88.123456,
            'current_longitude' => -111.123456,
            'sports' => [
                ['id'=>$sports[0]->id],
                ['id'=>$sports[1]->id],
            ],
            'brands' => [
                ['id'=> $brands[0]->id],
                ['id'=> $brands[1]->id],
            ],
            'languages' => [
                ['id'=> $languages[0]->id],
                ['id'=> $languages[1]->id],
            ],

        ])->seeStatusCode(204);

        $response = $this->getJson('/user/1');

        $this->assertEquals('Köö', $response->data->first_name);
        $this->assertEquals('Käö', $response->data->last_name);
        $this->assertEquals('shirtname ö', $response->data->shirtname);
        $this->assertEquals('1980-12-12', $response->data->birthday);
        $this->assertEquals('male', $response->data->gender);
        $this->assertEquals('about me ö', $response->data->about_me);
        $this->assertEquals('sport quote ö', $response->data->sportquote);
        $this->assertEquals('current_city ö', $response->data->current_city);
        $this->assertEquals('current_country ö', $response->data->current_country);
        $this->assertEquals('FI', $response->data->current_country_code);
        $this->assertEquals('birth_country ö', $response->data->birth_country);
        $this->assertEquals($nationality->id, $response->data->nationality_id);
        $this->assertEquals(88.123456, $response->data->current_latitude, '', 0.000001);
        $this->assertEquals(-111.123456, $response->data->current_longitude, '', 0.000001);
        $this->assertEquals(2, count($response->data->sports));
        $this->assertEquals($sports[0]->id, $response->data->sports[0]->id);
        $this->assertEquals($sports[1]->id, $response->data->sports[1]->id);
        $this->assertEquals(2, count($response->data->brands));
        $this->assertEquals($brands[0]->id, $response->data->brands[0]->id);
        $this->assertEquals($brands[1]->id, $response->data->brands[1]->id);
        $this->assertEquals(2, count($response->data->languages));
        $this->assertEquals($languages[0]->id, $response->data->languages[0]->id);
        $this->assertEquals($languages[1]->id, $response->data->languages[1]->id);


        $this->put('/user', [
            'sports' => [
                ['id'=>$sports[5]->id],
                ['id'=>$sports[6]->id],
                ['id'=>$sports[7]->id],
            ],
            'brands' => [
                ['id'=> $brands[7]->id],
                ['id'=> $brands[8]->id],
                ['id'=> $brands[9]->id],
            ],
            'languages' => [
                ['id'=> $languages[9]->id],
                ['id'=> $languages[10]->id],
                ['id'=> $languages[11]->id],
            ],

        ])->seeStatusCode(204);

        $response = $this->getJson('/user/1');

        $this->assertEquals('Köö', $response->data->first_name);
        $this->assertEquals('Käö', $response->data->last_name);
        $this->assertEquals('shirtname ö', $response->data->shirtname);
        $this->assertEquals('1980-12-12', $response->data->birthday);
        $this->assertEquals('male', $response->data->gender);
        $this->assertEquals('about me ö', $response->data->about_me);
        $this->assertEquals('sport quote ö', $response->data->sportquote);
        $this->assertEquals('current_city ö', $response->data->current_city);
        $this->assertEquals('current_country ö', $response->data->current_country);
        $this->assertEquals('birth_country ö', $response->data->birth_country);
        $this->assertEquals($nationality->id, $response->data->nationality_id);
        $this->assertEquals(88.123456, $response->data->current_latitude, '', 0.000001);
        $this->assertEquals(-111.123456, $response->data->current_longitude, '', 0.000001);

        $this->assertEquals(3, count($response->data->sports));
        $this->assertEquals($sports[5]->id, $response->data->sports[0]->id);
        $this->assertEquals($sports[6]->id, $response->data->sports[1]->id);
        $this->assertEquals($sports[7]->id, $response->data->sports[2]->id);

        $this->assertEquals(3, count($response->data->brands));
        $this->assertEquals($brands[7]->id, $response->data->brands[0]->id);
        $this->assertEquals($brands[8]->id, $response->data->brands[1]->id);
        $this->assertEquals($brands[9]->id, $response->data->brands[2]->id);

        $this->assertEquals(3, count($response->data->languages));
        $this->assertEquals($languages[9]->id, $response->data->languages[0]->id);
        $this->assertEquals($languages[10]->id, $response->data->languages[1]->id);
        $this->assertEquals($languages[11]->id, $response->data->languages[2]->id);

    }

    public function testDeleteUser()
    {
        // disable authentication
        $this->withoutMiddleware();
        $user = factory(App\Models\Eloquent\User\User::class)->create([
            'password' => 'superSecret',
            'first_name' => 'Peter',
            'last_name' => 'Pan',
            'birthday' => '1979-12-14',
        ]);

        $this->actingAs($user);

        $this->get('/user/' . $user->id)->seeStatusCode(200);
        $this->delete('/user')->seeStatusCode(204);
        $this->seeInDatabase('user', [
            'email' => 'deleted-' . $user->id . '-' .$user->email,
            'id' => $user->id,
        ]);

        $this->get('/user/' . $user->id)->seeStatusCode(404);
    }
}
