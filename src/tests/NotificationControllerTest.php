<?php

use App\Models\Enum\NotificationStatus;
use App\Models\Enum\NotificationTypes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;

class NotificationControllerTest extends TestCase
{

    use DatabaseMigrations;

    public function testGetNotificationForUser()
    {
        $this->withoutMiddleware();

        $loggedInUser = factory(App\Models\Eloquent\User\User::class)->create();
        $friend_1 = factory(App\Models\Eloquent\User\User::class)->create();
        $friend_2 = factory(App\Models\Eloquent\User\User::class)->create();

        factory(App\Models\Eloquent\Notification\Notification::class)->create([
            'send_to' => $loggedInUser->id,
            'reference_user_id' => $friend_1->id,
            'message' => 'notification from friedn 1',
            'type' => NotificationTypes::CONNECTION_REQUEST_RECEIVED,
            'status' => NotificationStatus::NEW_NOTIFICATION,
        ]);

        factory(App\Models\Eloquent\Notification\Notification::class)->create([
            'send_to' => $loggedInUser->id,
            'reference_user_id' => $friend_2->id,
            'message' => 'notification from friedn 2',
            'type' => NotificationTypes::CONNECTION_REQUEST_RECEIVED,
            'status' => NotificationStatus::NEW_NOTIFICATION,
        ]);

        // this should not appear for logged in user
        factory(App\Models\Eloquent\Notification\Notification::class)->create([
            'send_to' => $friend_1->id,
            'reference_user_id' => $loggedInUser->id,
            'message' => 'notification from logged in user',
            'type' => NotificationTypes::CONNECTION_REQUEST_RECEIVED,
            'status' => NotificationStatus::NEW_NOTIFICATION,
        ]);

        $response = $this->getJson("/notifications/{$loggedInUser->id}");
        $this->assertEquals(2, count($response->data));
    }

    public function testMarkAsRead()
    {
        $this->withoutMiddleware();

        $friend_1 = factory(App\Models\Eloquent\User\User::class)->create();
        $friend_2 = factory(App\Models\Eloquent\User\User::class)->create();

        $notification = factory(App\Models\Eloquent\Notification\Notification::class)->create([
            'send_to' => $friend_1->id,
            'reference_user_id' => $friend_2->id,
            'message' => 'notification from friedn 1',
            'type' => NotificationTypes::CONNECTION_REQUEST_RECEIVED,
            'status' => NotificationStatus::NEW_NOTIFICATION,
        ]);

        $this->seeInDatabase('notification',[
            'id' =>  $notification->id,
            'status' => NotificationStatus::NEW_NOTIFICATION,
        ]);

        $this->put("/notifications/{$notification->id}/mark-as-read")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->seeInDatabase('notification',[
            'id' =>  $notification->id,
            'status' => NotificationStatus::READ,
        ]);
    }
}
