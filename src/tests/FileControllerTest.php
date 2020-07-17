<?php

use App\Models\Enum\ContactRequestStatus;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;

class FileControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreateImage()
    {
        $this->withoutMiddleware();
        $user = factory(App\Models\Eloquent\User\User::class)->create();
        $this->actingAs($user);

        $this->post('/files/image/profile_photo',[
            'base64_content'=>'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAAAAAA6fptVAAAACklEQVQYV2P4DwABAQEAWk1v8QAAAABJRU5ErkJggg==',
        ])->seeStatusCode(Response::HTTP_CREATED);


        $response = $this->call('GET', "/files/image/profile_photo/{$user->id}");
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertNotNull($response->getContent());


        $this->post('/files/image/cover_photo',[
            'base64_content'=>'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAAAAAA6fptVAAAACklEQVQYV2P4DwABAQEAWk1v8QAAAABJRU5ErkJggg==',
        ])->seeStatusCode(Response::HTTP_CREATED);

        $response = $this->call('GET', "/files/image/cover_photo/{$user->id}");
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertNotNull($response->getContent());
    }
}
