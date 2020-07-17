<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class FeedbackControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testFeedbackSend()
    {

        $this->withoutMiddleware();
        $user = factory(App\Models\Eloquent\User\User::class)->create();
        $this->actingAs($user);

        $this->post('/feedbacks', [
            'type' => 'bug',
            'description' => 'Test issue report']
        )->seeStatusCode(Response::HTTP_CREATED);

        $this->post('/feedbacks', [
            'type' => 'idea',
            'description' => 'Test improvement suggestion']
        )->seeStatusCode(Response::HTTP_CREATED);
    }
}
