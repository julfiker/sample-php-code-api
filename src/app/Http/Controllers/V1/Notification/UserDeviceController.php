<?php

namespace App\Http\Controllers\V1\Notification;

use App\Contracts\Activity\ActivityInterface;
use App\Contracts\Notification\PushNotificationInterface;
use App\Contracts\User\UserInterface;
use App\Http\Requests\RegisterDeviceRequest;
use App\Jobs\User\RegisterDevice;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Enum\NotificationTypes;
use Illuminate\Http\Response;

class UserDeviceController extends Controller
{

    public function register(RegisterDeviceRequest $request)
    {
        $userDevice = $this->dispatchFrom(RegisterDevice::class, $request);
        return response()->json(
            ['data' => $userDevice],
            Response::HTTP_CREATED,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    // this is a test method to test push notification, not for production use
    public function send(PushNotificationInterface $notificationInterface, UserInterface $userRepo, ActivityInterface $activityRepo){
        $user = $userRepo->find(1);
        $activity = $activityRepo->find(1);
        $notificationInterface->sendActivityNotification($user, $activity, NotificationTypes::INVITE_TO_ACTIVITY,"Hello world from Activity!");
    }
}
