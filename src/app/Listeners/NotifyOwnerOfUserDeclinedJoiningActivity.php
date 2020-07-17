<?php

namespace App\Listeners;

use App\Contracts\Notification\PushNotificationInterface;
use App\Contracts\User\UserInterface;
use App\Events\UserDeclinedActivity;
use App\Models\Enum\NotificationTypes;
use Carbon\Carbon;

class NotifyOwnerOfUserDeclinedJoiningActivity
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
     * @param  UserDeclinedActivity  $event
     * @return void
     */
    public function handle(UserDeclinedActivity $event)
    {
        $format = "%s is not joining %s";
        $message = sprintf(
            $format,
            $event->user->first_name,
            $event->activity->title
        );

        $owner = $this->userService->find($event->activity->owner_id);
        $this->notificationService->sendActivityNotification($owner,$event->activity, NotificationTypes::JOIN_ACTIVITY, $message);
    }


}
