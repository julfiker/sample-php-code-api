<?php

use App\Models\Enum\ActivityInvitationStatus;
use App\Models\Enum\NotificationTypes;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;

class ActivityControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Helper method to create a new Activity using a POST request
     * Creating activity using the post request allows invite other users to the event at the same time
     *
     * @param $data
     * @return mixed
     */
    private function createActivityByPostRequest($data)
    {
        return $this->postJson('/activities', [
            'sport_id'          => $data['sport_id'],
            'title'             => isset($data['title']) ? $data['title'] : 'Event Title',
            'start_time'        => isset($data['start_time']) ? $data['start_time'] : '2005-10-01T12:00:00+00:00',
            'end_time'          => isset($data['end_time']) ? $data['end_time'] : '2005-10-01T14:00:00+00:00',
            'description'       => isset($data['description']) ? $data['description'] : 'Description',
            'recurring'         => isset($data['recurring']) ? $data['recurring'] : 'no',
            'privacy'           => isset($data['privacy']) ? $data['privacy'] : 'open',
            'max_participants'  => isset($data['max_participants']) ? $data['max_participants'] : 10,
            'lat'               => isset($data['lat']) ? $data['lat'] : 88.123456,
            'long'              => isset($data['long']) ? $data['long'] : -111.123456,
            'country'           => isset($data['country']) ? $data['country'] : 'Finland',
            'country_code'      => isset($data['country_code']) ? $data['country_code'] : 'FI',
            'city'              => isset($data['city']) ? $data['city'] : 'Oulu',
            'address'           => isset($data['address']) ? $data['address'] : 'Address',
            'invite_users'      => isset($data['invite_users']) ? $data['invite_users'] : [],
        ])->data;
    }

    public function testCreateAndViewActivity()
    {
        $this->withoutMiddleware();

        $user = factory(App\Models\Eloquent\User\User::class)->create();
        $sport = factory(App\Models\Eloquent\Lists\Sport::class)->create();
        $this->actingAs($user);

        $activity_1 = $this->createActivityByPostRequest([
            'title' => 'title',
            'sport_id' => $sport->id,
            'start_time' => '2005-10-01T12:00:00+00:00',
            'end_time' => '2005-10-01T14:00:00+00:00',
            'description' => 'description',
            'recurring' => 'no',
            'privacy' => 'open',
            'max_participants' => 10,
            'lat' => 88.123456, // (+-90)
            'long' => -111.123456, //(+-180)
            'country' => 'Bangladesh',
            'country_code' => 'BD',
            'city' => 'Dhaka',
            'address' => 'Address',
        ]);

        $this->get("/activities/{$activity_1->id}")
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson([
                'title' => 'title',
                'owner_id' => $user->id,
                'sport_id' => $sport->id,
                'start_time' => '2005-10-01T12:00:00+00:00',
                'end_time' => '2005-10-01T14:00:00+00:00',
                'description' => 'description',
                'recurring' => 'no',
                'privacy' => 'open',
                'max_participants' => 10,
                'lat' => 88.123456, // (+-90)
                'long' => -111.123456, // (+-180)
                'country' => 'Bangladesh',
                'country_code' => 'BD',
                'city' => 'Dhaka',
                'address' => 'Address',
            ])->see('participant');

        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity_1->id,
            'user_id' => $user->id,
            'status' => ActivityInvitationStatus::JOINING,
        ]);


        // create activity and also invite users at the same time
        $user_1 = factory(App\Models\Eloquent\User\User::class)->create();
        $user_2 = factory(App\Models\Eloquent\User\User::class)->create();
        $user_3 = factory(App\Models\Eloquent\User\User::class)->create();
        $user_4 = factory(App\Models\Eloquent\User\User::class)->create();
        $this->actingAs($user);
        $activity_2 = $this->createActivityByPostRequest([
            'title' => 'title 2',
            'sport_id' => $sport->id,
            'start_time' => '2005-10-01T12:00:00+00:00',
            'end_time' => '2005-10-01T14:00:00+00:00',
            'description' => 'description',
            'recurring' => 'no',
            'privacy' => 'open',
            'max_participants' => 10,
            'lat' => 88.123456, // (+-90)
            'long' => -111.123456, //(+-180)
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
            'invite_users' => [
                ['id' => $user_1->id],
                ['id' => $user_2->id],
                ['id' => $user_3->id],
                ['id' => $user_4->id],
            ]
        ]);

        // should add user to activity with proper status
        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity_2->id,
            'user_id' => $user->id,
            'status' => ActivityInvitationStatus::JOINING,
        ]);
        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity_2->id,
            'user_id' => $user_1->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);
        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity_2->id,
            'user_id' => $user_2->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);
        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity_2->id,
            'user_id' => $user_3->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);
        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity_2->id,
            'user_id' => $user_4->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);

        // should send notifications
        $this->seeInDatabase('notification', [
            'send_to' => $user_1->id,
            'reference_activity_id' => $activity_2->id,
            'type' => 'INVITE_TO_ACTIVITY',
        ]);
        $this->seeInDatabase('notification', [
            'send_to' => $user_2->id,
            'reference_activity_id' => $activity_2->id,
            'type' => 'INVITE_TO_ACTIVITY',
        ]);
        $this->seeInDatabase('notification', [
            'send_to' => $user_3->id,
            'reference_activity_id' => $activity_2->id,
            'type' => 'INVITE_TO_ACTIVITY',
        ]);
        $this->seeInDatabase('notification', [
            'send_to' => $user_4->id,
            'reference_activity_id' => $activity_2->id,
            'type' => 'INVITE_TO_ACTIVITY',
        ]);
    }


    public function testListOwnActivity()
    {
        $this->withoutMiddleware();

        $user = factory(App\Models\Eloquent\User\User::class)->create();
        $sport = factory(App\Models\Eloquent\Lists\Sport::class)->create();
        $this->actingAs($user);

        factory(App\Models\Eloquent\Activity\Activity::class, 10)->create([
            'owner_id' => $user->id,
            'sport_id' => $sport->id,
            'start_time' => '2015-01-02T12:00:00+00:00',
            'end_time' => '2015-01-02T14:00:00+00:00',
        ]);
        factory(App\Models\Eloquent\Activity\Activity::class, 20)->create([
            'owner_id' => $user->id,
            'sport_id' => $sport->id,
            'start_time' => '2015-06-01T12:00:00+00:00',
            'end_time' => '2015-06-01T14:00:00+00:00',
        ]);
        factory(App\Models\Eloquent\Activity\Activity::class, 30)->create([
            'owner_id' => $user->id,
            'sport_id' => $sport->id,
            'start_time' => '2015-12-01T12:00:00+00:00',
            'end_time' => '2015-12-01T14:00:00+00:00',
        ]);

        $response = $this->getJson("users/{$user->id}/activities");
        $this->assertEquals(20, $response->per_page);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(3, $response->last_page);
        $this->assertEquals(60, $response->total);

        $response = $this->getJson("users/{$user->id}/activities?month=01&year=2015");
        $this->assertEquals(20, $response->per_page);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(10, $response->total);

        $response = $this->getJson("users/{$user->id}/activities?month=06&year=2015");
        $this->assertEquals(20, $response->per_page);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(20, $response->total);

        $response = $this->getJson("users/{$user->id}/activities?month=12&year=2015");
        $this->assertEquals(20, $response->per_page);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(2, $response->last_page);
        $this->assertEquals(30, $response->total);
    }

    public function testListJoinedInvitedActivity()
    {
        $this->withoutMiddleware();

        $mainUser = factory(App\Models\Eloquent\User\User::class)->create();

        $otherUser = factory(App\Models\Eloquent\User\User::class)->create();

        $sport = factory(App\Models\Eloquent\Lists\Sport::class)->create();
        $this->actingAs($mainUser);

        $activities_1 = factory(App\Models\Eloquent\Activity\Activity::class, 5)->create([
            'owner_id' => $otherUser->id,
            'sport_id' => $sport->id,
            'start_time' => '2015-01-01T12:00:00+00:00',
            'end_time' => '2015-01-01T14:00:00+00:00',
        ]);

        foreach ($activities_1 as $activity) {
            // main user is joining
            $this->actingAs($mainUser);
            $this->put("/activities/{$activity->id}/join")
                ->seeStatusCode(Response::HTTP_NO_CONTENT);
        }

        $activities_2 = factory(App\Models\Eloquent\Activity\Activity::class, 15)->create([
            'owner_id' => $otherUser->id,
            'sport_id' => $sport->id,
            'start_time' => '2015-06-01T12:00:00+00:00',
            'end_time' => '2015-06-01T14:00:00+00:00',
        ]);

        foreach ($activities_2 as $activity) {
            // main user is joining
            $this->actingAs($mainUser);
            $this->put("/activities/{$activity->id}/join")
                ->seeStatusCode(Response::HTTP_NO_CONTENT);
        }

        $activities_3 = factory(App\Models\Eloquent\Activity\Activity::class, 30)->create([
            'owner_id' => $otherUser->id,
            'sport_id' => $sport->id,
            'start_time' => '2015-12-01T12:00:00+00:00',
            'end_time' => '2015-12-01T14:00:00+00:00',
        ]);

        $counter = 0;
        foreach ($activities_3 as $activity) {
            // main user is joining in 10 activities -----------------
            $this->actingAs($mainUser);
            $this->put("/activities/{$activity->id}/join")
                ->seeStatusCode(Response::HTTP_NO_CONTENT);
            $counter++;

            if ($counter >= 10) {
                break;
            }
        }

        $response = $this->getJson("users/{$mainUser->id}/activities/joined");
        $this->assertEquals(20, $response->per_page);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(2, $response->last_page);
        $this->assertEquals(30, $response->total);

        $response = $this->getJson("users/{$mainUser->id}/activities/joined?month=01&year=2015");
        $this->assertEquals(20, $response->per_page);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(5, $response->total);

        $response = $this->getJson("users/{$mainUser->id}/activities/joined?month=06&year=2015");
        $this->assertEquals(20, $response->per_page);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(15, $response->total);

        $response = $this->getJson("users/{$mainUser->id}/activities/joined?month=12&year=2015");
        $this->assertEquals(20, $response->per_page);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(10, $response->total);
    }


    public function testListJoinedFutureActivity()
    {
        $this->withoutMiddleware();

        $mainUser = factory(App\Models\Eloquent\User\User::class)->create();

        $otherUser = factory(App\Models\Eloquent\User\User::class)->create();

        $sport = factory(App\Models\Eloquent\Lists\Sport::class)->create();
        $this->actingAs($mainUser);

        $datetime = new DateTime('now + 15 minutes');
        $today = $datetime->format('Y-m-d\TH:i:s+00:00');

        $datetime = new DateTime('tomorrow');
        $tomorrow = $datetime->format('Y-m-d\TH:i:s+00:00');

        $ownActivityOld = $this->createActivityByPostRequest([
            'sport_id' => $sport->id,
            'start_time' => '2015-01-01T12:00:00+00:00',
            'end_time' => '2015-01-01T14:00:00+00:00',
            'privacy' => 'open',
            'max_participants' => 10,
        ]);
        $response = $this->getJson("users/{$mainUser->id}/activities/joined/future");
        $this->assertEquals(0, $response->total); // my old event, should not appear


        $invitedFutureActivities = factory(App\Models\Eloquent\Activity\Activity::class, 5)->create([
            'owner_id' => $otherUser->id,
            'sport_id' => $sport->id,
            'start_time' => '2015-01-01T12:00:00+00:00',
            'end_time' => '2015-01-01T14:00:00+00:00',
        ]);
        foreach ($invitedFutureActivities as $activity) {
            // main user is joining
            $this->actingAs($mainUser);
            $this->put("/activities/{$activity->id}/join")
                ->seeStatusCode(Response::HTTP_NO_CONTENT);
        }

        $response = $this->getJson("users/{$mainUser->id}/activities/joined/future");
        $this->assertEquals(0, $response->total); // other's old events, should be here


        $ownActivityToday = $this->createActivityByPostRequest([
            'sport_id' => $sport->id,
            'start_time' => $today,
            'end_time' => $today,
            'privacy' => 'open',
            'max_participants' => 10,
        ]);
        $response = $this->getJson("users/{$mainUser->id}/activities/joined/future");
        $this->assertEquals(1, $response->total); // today's event, should appear

        $ownActivityFuture = $this->createActivityByPostRequest([
            'sport_id' => $sport->id,
            'start_time' => $tomorrow,
            'end_time' => $tomorrow,
            'privacy' => 'open',
            'max_participants' => 10,
        ]);

        $response = $this->getJson("users/{$mainUser->id}/activities/joined/future");
        $this->assertEquals(1 + 1 , $response->total); // future event, should be here


        $invitedFutureActivities = factory(App\Models\Eloquent\Activity\Activity::class, 5)->create([
            'owner_id' => $otherUser->id,
            'sport_id' => $sport->id,
            'start_time' => $tomorrow,
            'end_time' => $tomorrow,
        ]);
        foreach ($invitedFutureActivities as $activity) {
            // main user is joining
            $this->actingAs($mainUser);
            $this->put("/activities/{$activity->id}/join")
                ->seeStatusCode(Response::HTTP_NO_CONTENT);
        }

        $response = $this->getJson("users/{$mainUser->id}/activities/joined/future");
        $this->assertEquals(1 + 1 + 5, $response->total); // other's old events, should be here
    }


    public function testListInvitedActivity()
    {
        $this->withoutMiddleware();

        $mainUser = factory(App\Models\Eloquent\User\User::class)->create();

        $user_1 = factory(App\Models\Eloquent\User\User::class)->create();
        $user_2 = factory(App\Models\Eloquent\User\User::class)->create();

        $sport = factory(App\Models\Eloquent\Lists\Sport::class)->create();
        $this->actingAs($mainUser);

        $activity_1 = $this->createActivityByPostRequest([
            'sport_id' => $sport->id,
            'start_time' => '2015-10-01T12:00:00+00:00',
            'end_time' => '2015-10-01T14:00:00+00:00',
            'privacy' => 'open',
            'max_participants' => 10,
            'invite_users' => [
                ['id' => $user_1->id],
                ['id' => $user_2->id],
            ]
        ]);

        $activity_2 = $this->createActivityByPostRequest([
            'sport_id' => $sport->id,
            'start_time' => '2015-10-01T12:00:00+00:00',
            'end_time' => '2015-10-01T14:00:00+00:00',
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
            'invite_users' => [
                ['id' => $user_2->id],
            ]
        ]);

        // check the invited activity list for user_1
        $response = $this->getJson("users/{$user_1->id}/activities/invited?month=10&year=2015");
        $this->assertEquals(20, $response->per_page);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(1, $response->total);

        $response = $this->getJson("users/{$user_1->id}/activities/invited?month=09&year=2015");
        $this->assertEquals(0, $response->total);
        $response = $this->getJson("users/{$user_1->id}/activities/invited?month=11&year=2015");
        $this->assertEquals(0, $response->total);


        // check the invited activity list for user_2
        $response = $this->getJson("users/{$user_2->id}/activities/invited?month=10&year=2015");
        $this->assertEquals(20, $response->per_page);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(2, $response->total);

        $response = $this->getJson("users/{$user_2->id}/activities/invited?month=09&year=2015");
        $this->assertEquals(0, $response->total);
        $response = $this->getJson("users/{$user_2->id}/activities/invited?month=11&year=2015");
        $this->assertEquals(0, $response->total);

        // user_2 joins the activity_1, so the event should be removed from the list
        $this->actingAs($user_2);
        $this->put("/activities/{$activity_1->id}/join")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);
        $response = $this->getJson("users/{$user_2->id}/activities/invited?month=10&year=2015");
        $this->assertEquals(1, $response->total);

        // user_2 declines the activity_2, so the event should be removed from the list
        $this->actingAs($user_2);
        $this->put("/activities/{$activity_2->id}/decline")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);
        $response = $this->getJson("users/{$user_2->id}/activities/invited?month=10&year=2015");
        $this->assertEquals(0, $response->total);
    }


    public function testListInvitedFutureActivity()
    {
        $this->withoutMiddleware();

        $mainUser = factory(App\Models\Eloquent\User\User::class)->create();

        $invitedUser = factory(App\Models\Eloquent\User\User::class)->create();

        $sport = factory(App\Models\Eloquent\Lists\Sport::class)->create();


        $datetime = new DateTime('now + 15 minutes');
        $today = $datetime->format('Y-m-d\TH:i:s+00:00');


        $datetime = new DateTime('tomorrow');
        $tomorrow = $datetime->format('Y-m-d\TH:i:s+00:00');

        $this->actingAs($mainUser);
        $activity_1 = $this->createActivityByPostRequest([
            'sport_id' => $sport->id,
            'start_time' => '2015-10-01T12:00:00+00:00',
            'end_time' => '2015-10-01T14:00:00+00:00',
            'privacy' => 'open',
            'max_participants' => 10,
            'invite_users' => [
                ['id' => $invitedUser->id],
            ]
        ]);

        $this->actingAs($invitedUser);
        $response = $this->getJson("users/{$invitedUser->id}/activities/invited/future");
        $this->assertEquals(0, $response->total); // old event, should not appear

        $this->actingAs($mainUser);
        $activity_1 = $this->createActivityByPostRequest([
            'sport_id' => $sport->id,
            'start_time' => $today,
            'end_time' => $today,
            'privacy' => 'open',
            'max_participants' => 10,
            'invite_users' => [
                ['id' => $invitedUser->id],
            ]
        ]);

        $this->actingAs($invitedUser);
        $response = $this->getJson("users/{$invitedUser->id}/activities/invited/future");
        $this->assertEquals(1, $response->total); // current event, should appear


        $this->actingAs($mainUser);
        $activity_1 = $this->createActivityByPostRequest([
            'sport_id' => $sport->id,
            'start_time' => $tomorrow,
            'end_time' => $tomorrow,
            'privacy' => 'open',
            'max_participants' => 10,
            'invite_users' => [
                ['id' => $invitedUser->id],
            ]
        ]);

        $this->actingAs($invitedUser);
        $response = $this->getJson("users/{$invitedUser->id}/activities/invited/future");
        $this->assertEquals(1 + 1, $response->total); // future event, should appear
    }


    public function testUpdateActivity()
    {
        $this->withoutMiddleware();

        $mainUser = factory(App\Models\Eloquent\User\User::class)->create();
        $user_1 = factory(App\Models\Eloquent\User\User::class)->create();
        $user_2 = factory(App\Models\Eloquent\User\User::class)->create();
        $user_3 = factory(App\Models\Eloquent\User\User::class)->create();
        $user_4 = factory(App\Models\Eloquent\User\User::class)->create();
        $sport = factory(App\Models\Eloquent\Lists\Sport::class)->create();
        $this->actingAs($mainUser);

        $activity = $this->createActivityByPostRequest([
            'title' => 'title',
            'owner_id' => $mainUser->id,
            'sport_id' => $sport->id,
            'start_time' => '2005-10-01T12:00:00+00:00',
            'end_time' => '2005-10-01T14:00:00+00:00',
            'description' => 'description',
            'recurring' => 'no',
            'privacy' => 'open',
            'max_participants' => 10,
            'lat' => 88.123456, // (+-90)
            'long' => -111.123456, //(+-180)
            'country' => 'Bangladesh',
            'country_code' => 'BD',
            'city' => 'Dhaka',
            'address' => 'Address',
            'invite_users' => [
                ['id' => $user_1->id]
            ]
        ]);

        // check if users are added to activity
        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $mainUser->id,
            'status' => ActivityInvitationStatus::JOINING,
        ]);
        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $user_1->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);

        // should send notifications
        $this->seeInDatabase('notification', [
            'send_to' => $user_1->id,
            'reference_activity_id' => $activity->id,
            'type' => 'INVITE_TO_ACTIVITY',
        ]);

        $this->put("/activities/{$activity->id}", [
            'id' => $activity->id,
            'title' => 'updated_title',
            'sport_id' => $sport->id,
            'start_time' => '2005-02-01T12:00:00+01:00',
            'end_time' => '2005-02-01T14:00:00+01:00',
            'description' => 'updated_description',
            'recurring' => 'daily',
            'privacy' => 'open',
            'max_participants' => 20,
            'lat' => 77.123456, // (+-90)
            'long' => -11.123456, //(+-180)
            'country' => 'Finland',
            'country_code' => 'FI',
            'city' => 'Helsinki',
            'address' => 'Updated Address',
            'participants' => [
                ['id' => $user_2->id],
                ['id' => $user_3->id],
                ['id' => $user_4->id],
            ]
        ])
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->get("/activities/{$activity->id}")
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson([
                    'title' => 'updated_title',
                    'owner_id' => $mainUser->id,
                    'sport_id' => $sport->id,
                    'start_time' => '2005-02-01T11:00:00+00:00',
                    'end_time' => '2005-02-01T13:00:00+00:00',
                    'description' => 'updated_description',
                    'recurring' => 'daily',
                    'privacy' => 'open',
                    'max_participants' => 20,
                    'lat' => 77.123456, // (+-90)
                    'long' => -11.123456, //(+-180)
                    'country' => 'Finland',
                    'city' => 'Helsinki',
                    'country_code' => 'FI',
                    'address' => 'Updated Address',
                ]
            );

        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $user_2->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);
        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $user_3->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);
        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $user_4->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);


        // should send notifications
        $this->seeInDatabase('notification', [
            'send_to' => $user_1->id,
            'reference_activity_id' => $activity->id,
            'type' => NotificationTypes::INVITE_TO_ACTIVITY,
        ]);
        $this->seeInDatabase('notification', [
            'send_to' => $user_2->id,
            'reference_activity_id' => $activity->id,
            'type' => NotificationTypes::INVITE_TO_ACTIVITY,
        ]);
        $this->seeInDatabase('notification', [
            'send_to' => $user_3->id,
            'reference_activity_id' => $activity->id,
            'type' => NotificationTypes::INVITE_TO_ACTIVITY,
        ]);
        $this->seeInDatabase('notification', [
            'send_to' => $user_4->id,
            'reference_activity_id' => $activity->id,
            'type' => NotificationTypes::INVITE_TO_ACTIVITY,
        ]);
    }

    public function testDeleteActivity()
    {
        $this->withoutMiddleware();

        $user = factory(App\Models\Eloquent\User\User::class)->create();
        $user_1 = factory(App\Models\Eloquent\User\User::class)->create();
        $sport = factory(App\Models\Eloquent\Lists\Sport::class)->create();
        $this->actingAs($user);

        $activity = $this->createActivityByPostRequest([
            'title' => 'title',
            'owner_id' => $user->id,
            'sport_id' => $sport->id,
            'invite_users' => [
                ['id' => $user_1->id]
            ]
        ]);

        $this->actingAs($user_1);
        $this->put("/activities/{$activity->id}/join")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->delete("/activities/{$activity->id}")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->get("/activities/{$activity->id}")
            ->seeStatusCode(Response::HTTP_NOT_FOUND);

        $this->seeInDatabase('notification', [
            'send_to' => $user_1->id,
            'reference_activity_id' => $activity->id,
            'type' => NotificationTypes::CANCEL_ACTIVITY,
        ]);
    }


    public function testInviteJoinLeaveActivity()
    {
        $this->withoutMiddleware();

        $this->expectsEvents([
            App\Events\UsersInvitedToActivity::class,
            App\Events\UserDeclinedActivity::class,
            App\Events\UserJoinedActivity::class,
            App\Events\UserLeftActivity::class,
        ]);

        $owner = factory(App\Models\Eloquent\User\User::class)->create();
        $friend_1 = factory(App\Models\Eloquent\User\User::class)->create();
        $friend_2 = factory(App\Models\Eloquent\User\User::class)->create();
        $friend_3 = factory(App\Models\Eloquent\User\User::class)->create();
        $sport = factory(App\Models\Eloquent\Lists\Sport::class)->create();
        $this->actingAs($owner);

        $activity = $this->createActivityByPostRequest([
            'owner_id' => $owner->id,
            'sport_id' => $sport->id,
            'start_time' => '2005-10-01T12:00:00+00:00',
            'end_time' => '2005-10-01T14:00:00+00:00',
            'privacy' => 'open',
            'max_participants' => 10,
        ]);


        $this->put("/activities/{$activity->id}/invite", [
            'users' => [$friend_1->id, $friend_2->id, $friend_3->id],
        ])->seeStatusCode(Response::HTTP_NO_CONTENT);


        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $owner->id,
            'status' => ActivityInvitationStatus::JOINING,
        ]);

        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $friend_1->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);

        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $friend_2->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);
        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $friend_3->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);

        // friend 1 is joining  -----------------
        $this->actingAs($friend_1);
        $this->put("/activities/{$activity->id}/join")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $friend_1->id,
            'status' => ActivityInvitationStatus::JOINING,
        ]);

        $this->missingFromDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $friend_1->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);

        // friend 2 cancelled (left) the invitation  -----------------
        $this->actingAs($friend_2);
        $this->put("/activities/{$activity->id}/leave")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $friend_2->id,
            'status' => ActivityInvitationStatus::LEFT,
        ]);

        $this->missingFromDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $friend_2->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);

        // friend 2 should not allowed to join again once he left
        $this->actingAs($friend_2);
        $this->put("/activities/{$activity->id}/join")
            ->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $owner->id,
            'status' => ActivityInvitationStatus::JOINING,
        ]);

        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $friend_1->id,
            'status' => ActivityInvitationStatus::JOINING,
        ]);

        // friend 3 declined joining -----------------
        $this->actingAs($friend_3);
        $this->put("/activities/{$activity->id}/decline")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->seeInDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $friend_3->id,
            'status' => ActivityInvitationStatus::DECLINED,
        ]);

        $this->missingFromDatabase('pivot_activity_user', [
            'activity_id' => $activity->id,
            'user_id' => $friend_3->id,
            'status' => ActivityInvitationStatus::INVITED,
        ]);


        // verify that owner and friend_1 is only in the participant list
        $this->actingAs($owner);
        $response = $this->getJson("/activities/{$activity->id}");
        $this->assertEquals(2, count($response->data->participants));
    }


    public function testJoinLimit()
    {
        $this->withoutMiddleware();

        $owner = factory(App\Models\Eloquent\User\User::class)->create();
        $friend_1 = factory(App\Models\Eloquent\User\User::class)->create();
        $friend_2 = factory(App\Models\Eloquent\User\User::class)->create();
        $friend_3 = factory(App\Models\Eloquent\User\User::class)->create();
        $sport = factory(App\Models\Eloquent\Lists\Sport::class)->create();
        $this->actingAs($owner);

        $activity = $this->createActivityByPostRequest([
            'owner_id' => $owner->id,
            'sport_id' => $sport->id,
            'privacy' => 'open',
            'max_participants' => 3,
        ]);

        $this->put("/activities/{$activity->id}/invite", [
            'users' => [$friend_1->id, $friend_2->id, $friend_3->id],
        ])->seeStatusCode(Response::HTTP_NO_CONTENT);

        // friend 1 is joining
        $this->actingAs($friend_1);
        $this->put("/activities/{$activity->id}/join")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        // friend 2 is joining
        $this->actingAs($friend_2);
        $this->put("/activities/{$activity->id}/join")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        // friend 3 is joining - should not be allowed - already reached the limit
        $this->actingAs($friend_3);
        $this->put("/activities/{$activity->id}/join")
            ->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testExplore()
    {
        $this->withoutMiddleware();
        $boating = factory(App\Models\Eloquent\Lists\Sport::class)->create();
        $chess = factory(App\Models\Eloquent\Lists\Sport::class)->create();

        $location = [
            'current_country' => 'Bangladesh',
            'current_city' => 'Dhaka',
        ];
        $user_1 = factory(App\Models\Eloquent\User\User::class)->create($location);
        $user_1->sports()->attach($boating->id);
        $owner = factory(App\Models\Eloquent\User\User::class)->create($location);
        $owner->sports()->attach($boating->id);
        $user_3 = factory(App\Models\Eloquent\User\User::class)->create($location);
        $user_3->sports()->attach($chess->id);

        $futureStartTime = Carbon::now()->addDay(1)->toAtomString();
        $futureEndTime = Carbon::now()->addDay(2)->toAtomString();

        // ---------------------------------------------------------------------------
        // boating - old activities // should not appear
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 5)->create([
            'owner_id' => $user_1->id,
            'sport_id' => $boating->id,
            'start_time' => '2005-10-01T12:00:00+00:00',
            'end_time' => '2005-10-01T14:00:00+00:00',
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);


        $this->actingAs($owner);
        $response = $this->getJson("/activities/explore");
        $this->assertEquals(0, $response->total);

        // ---------------------------------------------------------------------------
        // boating new activity, but owner's
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 5)->create([
            'owner_id' => $owner->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);

        $this->actingAs($owner);
        $response = $this->getJson("/activities/explore");
        $this->assertEquals(0, $response->total);

        // ---------------------------------------------------------------------------
        // should not appear as the sport is chess
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 5)->create([
            'owner_id' => $user_3->id,
            'sport_id' => $chess->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);

        $this->actingAs($owner);
        $response = $this->getJson("/activities/explore");
        $this->assertEquals(0, $response->total);

        // ---------------------------------------------------------------------------
        // should not appear  as privacy =  closed
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 5)->create([
            'owner_id' => $user_3->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'closed',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);

        $this->actingAs($owner);
        $response = $this->getJson("/activities/explore");
        $this->assertEquals(0, $response->total);

        // ---------------------------------------------------------------------------
        // should not appear - not same city
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 5)->create([
            'owner_id' => $user_3->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Mymensingh',
        ]);

        $this->actingAs($owner);
        $response = $this->getJson("/activities/explore");
        $this->assertEquals(0, $response->total);

        // ---------------------------------------------------------------------------
        // should not appear - max_participant is reached
        // ---------------------------------------------------------------------------

        // instead of using factory, actual API is call so that owner is added as a participant during creating an activity
        $this->actingAs($user_3);
        $activity = $this->createActivityByPostRequest([
            'owner_id' => $user_3->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 1,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);

        $this->actingAs($owner);
        $response = $this->getJson("/activities/explore");
        $this->assertEquals(0, $response->total);

        // ---------------------------------------------------------------------------
        // should not  appear as user is already got invite or joined
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 1)->create([
            'owner_id' => $user_3->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);
        $this->actingAs($user_3);
        $this->put("/activities/{$activity->id}/invite", [
            'users' => [$owner->id],
        ])->seeStatusCode(Response::HTTP_NO_CONTENT);

        // owner is already invited to the activity, so should not appear
        $this->actingAs($owner);
        $response = $this->getJson("/activities/explore");
        $this->assertEquals(0, $response->total);


        // user_3 is joining
        $this->actingAs($owner);
        $this->put("/activities/{$activity->id}/join")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        // owner has joined to the activity
        $this->actingAs($owner);
        $response = $this->getJson("/activities/explore");
        $this->assertEquals(0, $response->total);

        // ---------------------------------------------------------------------------
        // should appear
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 5)->create([
            'owner_id' => $user_3->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);

        $this->actingAs($owner);
        $response = $this->getJson("/activities/explore");
        $this->assertEquals(5, $response->total);
    }

    public function testSearchWithoutParameters()
    {
        $this->withoutMiddleware();
        $boating = factory(App\Models\Eloquent\Lists\Sport::class)->create();
        $chess = factory(App\Models\Eloquent\Lists\Sport::class)->create();


        $owner = factory(App\Models\Eloquent\User\User::class)->create();
        $owner->sports()->attach($boating->id);

        $user_1 = factory(App\Models\Eloquent\User\User::class)->create();
        $user_1->sports()->attach($boating->id);

        $user_3 = factory(App\Models\Eloquent\User\User::class)->create();
        $user_3->sports()->attach($chess->id);

        $futureStartTime = Carbon::now()->addDay(1)->toAtomString();
        $futureEndTime = Carbon::now()->addDay(2)->toAtomString();


        // ---------------------------------------------------------------------------
        // boating - old activities // should not appear
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 5)->create([
            'owner_id' => $user_1->id,
            'sport_id' => $boating->id,
            'start_time' => '2005-10-01T12:00:00+00:00',
            'end_time' => '2005-10-01T14:00:00+00:00',
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);


        $this->actingAs($owner);
        $response = $this->getJson("/activities/search");
        $this->assertEquals(0, $response->total);

        // ---------------------------------------------------------------------------
        // should not appear  as privacy =  closed
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 1)->create([
            'owner_id' => $user_3->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'closed',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);

        $this->actingAs($owner);
        $response = $this->getJson("/activities/search");
        $this->assertEquals(0, $response->total);

        // ---------------------------------------------------------------------------
        // should not appear - max_participant is reached
        // ---------------------------------------------------------------------------

        // instead of using factory, actual API is call so that owner is added as a participant during creating an activity
        $this->actingAs($owner);
        $activity = $this->createActivityByPostRequest([
            'owner_id' => $owner->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 1,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);

        $response = $this->getJson("/activities/search");
        $this->assertEquals(0, $response->total);


        // ---------------------------------------------------------------------------
        // boating new activity, but owner's -- should appear
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 1)->create([
            'owner_id' => $owner->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);

        $this->actingAs($owner);
        $response = $this->getJson("/activities/search");
        $this->assertEquals(1, $response->total);

        // ---------------------------------------------------------------------------
        // should appear -- now the sport is chess
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 1)->create([
            'owner_id' => $user_3->id,
            'sport_id' => $chess->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);

        $this->actingAs($owner);
        $response = $this->getJson("/activities/search");
        $this->assertEquals(2, $response->total);


        // ---------------------------------------------------------------------------
        // should appear - though not same city
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 1)->create([
            'owner_id' => $user_3->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Mymensingh',
        ]);

        $this->actingAs($owner);
        $response = $this->getJson("/activities/search");
        $this->assertEquals(3, $response->total);

        // ---------------------------------------------------------------------------
        // should appear - though user already got invited or joined
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 1)->create([
            'owner_id' => $user_3->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);
        $this->actingAs($user_3);
        $this->put("/activities/{$activity->id}/invite", [
            'users' => [$owner->id],
        ])->seeStatusCode(Response::HTTP_NO_CONTENT);

        // owner is already invited to the activity, so should not appear
        $this->actingAs($owner);
        $response = $this->getJson("/activities/search");
        $this->assertEquals(4, $response->total);


        // user_3 is joining
        $this->actingAs($owner);
        $this->put("/activities/{$activity->id}/join")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        // owner has joined to the activity
        $this->actingAs($owner);
        $response = $this->getJson("/activities/search");
        $this->assertEquals(4, $response->total);

        // ---------------------------------------------------------------------------
        // should appear
        // ---------------------------------------------------------------------------
        $activity = factory(App\Models\Eloquent\Activity\Activity::class, 1)->create([
            'owner_id' => $user_3->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);

        $this->actingAs($owner);
        $response = $this->getJson("/activities/search");
        $this->assertEquals(5, $response->total);

    }

    public function testSearchWithParameters()
    {
        $this->withoutMiddleware();
        $boating = factory(App\Models\Eloquent\Lists\Sport::class)->create();
        $chess = factory(App\Models\Eloquent\Lists\Sport::class)->create();

        $user = factory(App\Models\Eloquent\User\User::class)->create();
        $user->sports()->attach($boating->id);

        $futureStartTime = Carbon::now()->addDay(1)->toAtomString();
        $futureEndTime = Carbon::now()->addDay(2)->toAtomString();

        factory(App\Models\Eloquent\Activity\Activity::class, 1)->create([
            'owner_id' => $user->id,
            'sport_id' => $boating->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);

        factory(App\Models\Eloquent\Activity\Activity::class, 1)->create([
            'owner_id' => $user->id,
            'sport_id' => $chess->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);

        factory(App\Models\Eloquent\Activity\Activity::class, 1)->create([
            'owner_id' => $user->id,
            'sport_id' => $chess->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Bangladesh',
            'city' => 'Mymensingh',
        ]);

        factory(App\Models\Eloquent\Activity\Activity::class, 1)->create([
            'owner_id' => $user->id,
            'sport_id' => $chess->id,
            'start_time' => $futureStartTime,
            'end_time' => $futureEndTime,
            'privacy' => 'open',
            'max_participants' => 10,
            'country' => 'Finland',
            'city' => 'Oulu',
        ]);

        // search by sport
        $response = $this->getJson("/activities/search", 200, ['sport_id' => 9999999]);
        $this->assertEquals(0, $response->total);
        $response = $this->getJson("/activities/search", 200, ['sport_id' => $boating->id]);
        $this->assertEquals(1, $response->total);
        $response = $this->getJson("/activities/search", 200, ['sport_id' => $chess->id]);
        $this->assertEquals(3, $response->total);

        // search by country
        $response = $this->getJson("/activities/search", 200, ['country' => 'Thailand']);
        $this->assertEquals(0, $response->total);
        $response = $this->getJson("/activities/search", 200, ['country' => 'Bangladesh']);
        $this->assertEquals(3, $response->total);
        $response = $this->getJson("/activities/search", 200, ['country' => 'Finland']);
        $this->assertEquals(1, $response->total);

        // search by city
        $response = $this->getJson("/activities/search", 200, ['city' => 'Bangkok']);
        $this->assertEquals(0, $response->total);
        $response = $this->getJson("/activities/search", 200, ['city' => 'Dhaka']);
        $this->assertEquals(2, $response->total);
        $response = $this->getJson("/activities/search", 200, ['city' => 'Mymensingh']);
        $this->assertEquals(1, $response->total);
        $response = $this->getJson("/activities/search", 200, ['city' => 'Oulu']);
        $this->assertEquals(1, $response->total);

        // combined search
        $response = $this->getJson("/activities/search", 200, [
            'sport_id' => $boating->id,
            'country' => 'Bangladesh',
            'city' => 'Dhaka',
        ]);
        $this->assertEquals(1, $response->total);
        $response = $this->getJson("/activities/search", 200, [
            'sport_id' => $chess->id,
            'country' => 'Bangladesh',
        ]);
        $this->assertEquals(2, $response->total);
    }

}