<?php

use App\Models\Enum\ContactRequestStatus;
use Illuminate\Http\Response;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function postJson($url, array $content = null, $statusCode = 201, $responseHeaders = null){
        $response = $this->call('POST', $url, $content);
        $this->assertEquals($statusCode, $response->status());
        $content = json_decode($response->getContent());
        $this->assertTrue(isset($content->data));

        $headerData = [];
        // check if the headers are available
        if (is_array($responseHeaders)) {
            foreach($responseHeaders as $headerName) {
                $headerData[$headerName] = $response->headers->get($headerName);
            }
        }

        if (count($headerData) > 0)
            return (object) array_merge( (array)$content, array( 'headers' => $headerData ) );
        else
            return $content;
    }

    public function getJson($url, $statusCode = 200, $parameters = []){
        $response = $this->call('GET', $url, $parameters);
        $this->assertEquals($statusCode, $response->status());
        return json_decode($response->getContent());
    }


    protected function sendInvitation($senderUser, $receiverUser){
        $this->actingAs($senderUser);
        return $this->post("/sport-contact-requests/{$receiverUser->id}/invite");
    }

    protected function acceptRequest($senderUser, $receiverUser) {
        $this->actingAs($receiverUser);
        $this->put("/sport-contact-requests/{$senderUser->id}/accept")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->seeInDatabase('sport_contact_request', [
            'to' => $receiverUser->id,
            'from' => $senderUser->id,
            'status' => ContactRequestStatus::ACCEPTED,
        ]);

        $this->seeInDatabase('sport_contact', [
            'self_id' => $receiverUser->id,
            'sport_contact_id' => $senderUser->id,
        ]);
    }

    protected function declineRequest($senderUser, $receiverUser) {
        $this->actingAs($receiverUser);
        $this->put("/sport-contact-requests/{$senderUser->id}/decline")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->seeInDatabase('sport_contact_request', [
            'from' => $senderUser->id,
            'to' => $receiverUser->id,
            'status' => ContactRequestStatus::DECLINED,
        ]);
    }

    protected function cancelRequest($senderUser, $receiverUser) {
        $this->actingAs($senderUser);
        $this->delete("/sport-contact-requests/{$receiverUser->id}/cancel")
            ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->seeInDatabase('sport_contact_request', [
            'from' => $senderUser->id,
            'to' => $receiverUser->id,
            'status' => ContactRequestStatus::CANCELLED,
        ]);
    }
}
