<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;

class UserDeviceControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test new device registration
     *
     * @return void
     */
    public function testNewUserDeviceRegistration()
    {
        $this->withoutMiddleware();
        $user = factory(App\Models\Eloquent\User\User::class)->create();
        // register a new device
        $this->post('/user-devices', [
            'user_id' => $user->id,
            'device_id' => 'dec301908b9ba8df85e57a58e40f96f523f4c2068674f5fe2ba25cdc250a2a41',
            'platform' => 'Android',
            'os' => 'Android 5.1',
        ])
        ->seeStatusCode(Response::HTTP_CREATED);

        $this->seeInDatabase('user_device', [
            'user_id' => $user->id,
            'device_id' => 'dec301908b9ba8df85e57a58e40f96f523f4c2068674f5fe2ba25cdc250a2a41',
            'platform' => 'Android',
            'os' => 'Android 5.1',
        ]);
    }
}
