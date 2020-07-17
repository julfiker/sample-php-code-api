<?php

use App\Models\Enum\ContactRequestStatus;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;

class SportContactControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testInviteAndAcceptAndUnfriend()
    {
        $this->withoutMiddleware();

        $this->expectsEvents([
            App\Events\SportContactRequestMade::class,
            App\Events\SportContactRequestAccepted::class,
        ]);

        $sender = factory(App\Models\Eloquent\User\User::class)->create();
        $receiver = factory(App\Models\Eloquent\User\User::class)->create();

        $this->sendInvitation($sender, $receiver)
            ->seeStatusCode(Response::HTTP_CREATED);

        $this->sendInvitation($sender, $receiver)
            ->seeStatusCode(Response::HTTP_CONFLICT)
            ->see('pending');

        $this->acceptRequest($sender, $receiver);

        // try to send friend request to accepted user [user1 to user2]
        $this->actingAs($sender);
        $this->post("/sport-contact-requests/{$receiver->id}/invite/")
            ->seeStatusCode(Response::HTTP_CONFLICT)
            ->see('connected');

        // try to send friend request to accepted user [user1 to user2]
        $this->actingAs($sender);
        $this->delete("/sport-contacts/{$receiver->id}")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->missingFromDatabase('sport_contact', [
           'self_id' => $sender->id,
           'sport_contact_id' => $receiver->id,
        ]);
        $this->missingFromDatabase('sport_contact', [
            'self_id' => $receiver->id,
            'sport_contact_id' => $sender->id,
        ]);
        // again send invitation from the user who deleted it
        $this->sendInvitation($receiver, $sender)
            ->seeStatusCode(Response::HTTP_CREATED);
    }

    public function testGetFriendList()
    {
        $this->withoutMiddleware();

        $this->expectsEvents([
            App\Events\SportContactRequestMade::class,
            App\Events\SportContactRequestAccepted::class,
        ]);

        $mainUser = factory(App\Models\Eloquent\User\User::class)->create();

        // Case 1: main user is creating request
        $userReceivingRequest = factory(App\Models\Eloquent\User\User::class)->create();
        $this->sendInvitation($mainUser, $userReceivingRequest)
            ->seeStatusCode(Response::HTTP_CREATED);

        $this->acceptRequest($mainUser, $userReceivingRequest);

        $this->actingAs($mainUser);
        $response = $this->getJson("/sport-contacts/{$mainUser->id}");
        $this->assertEquals(1, count($response->data));

        // Case 2: request is sent to main user
        $userSendingRequest = factory(App\Models\Eloquent\User\User::class)->create();
        $this->sendInvitation($userSendingRequest, $mainUser)
            ->seeStatusCode(Response::HTTP_CREATED);
        $this->acceptRequest($userSendingRequest, $mainUser);

        $this->actingAs($mainUser);
        $response = $this->getJson("/sport-contacts/{$mainUser->id}");
        $this->assertEquals(2, count($response->data));
    }

    public function testInviteAndDecline()
    {
        $this->withoutMiddleware();

        $sender = factory(App\Models\Eloquent\User\User::class)->create();
        $receiver = factory(App\Models\Eloquent\User\User::class)->create();

        // send friend request [user1 to user2]
        $this->sendInvitation($sender, $receiver)
            ->seeStatusCode(Response::HTTP_CREATED);

        $this->sendInvitation($sender, $receiver)
            ->seeStatusCode(Response::HTTP_CONFLICT)
            ->see('pending');

        $this->declineRequest($sender, $receiver);

        $this->actingAs($sender);
        $this->sendInvitation($sender, $receiver)
            ->seeStatusCode(Response::HTTP_CREATED);
    }



    public function testInviteAndCancel()
    {
        $this->withoutMiddleware();

        $sender = factory(App\Models\Eloquent\User\User::class)->create();
        $receiver = factory(App\Models\Eloquent\User\User::class)->create();

        // send friend request [user1 to user2]
        $this->sendInvitation($sender, $receiver)
            ->seeStatusCode(Response::HTTP_CREATED);

        $this->sendInvitation($sender, $receiver)
            ->seeStatusCode(Response::HTTP_CONFLICT)
            ->see('pending');

        $this->cancelRequest($sender, $receiver);

        $this->seeInDatabase('sport_contact_request', [
            'from' => $sender->id,
            'to' => $receiver->id,
            'status' => ContactRequestStatus::CANCELLED,
        ]);

        $this->actingAs($receiver);
        $this->put("/sport-contact-requests/$sender->id/accept")
            ->seeStatusCode(Response::HTTP_NOT_FOUND);

    }
}
