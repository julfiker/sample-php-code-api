<?php

namespace App\Listeners;

use App\Contracts\Notification\PushNotificationInterface;
use App\Contracts\User\UserInterface;
use App\Events\UserJoinedActivity;
use App\Models\Enum\NotificationTypes;
use Carbon\Carbon;

class NotifyOwnerOfUserJoiningActivity
{
    private $notificationService;
    private $userService;
    public function __construct(UserInterface $userService, PushNotificationInterface $notificationService){
        $this->notificationService = $notificationService;
        $this->userService = $userService;
    }
    /**
     * Handle the event.
     *
     * @param  UserJoinedActivity  $event
     * @return void
     */
    public function handle(UserJoinedActivity $event)
    {
        $owner = $this->userService->find($event->activity->owner_id);

        $format = "%s is joining %s";
        $message = sprintf(
            $format,
            $event->user->first_name,
            $event->activity->title
        );

        $this->notificationService->sendActivityNotification($owner,$event->activity, NotificationTypes::JOIN_ACTIVITY, $message);
    }


}
