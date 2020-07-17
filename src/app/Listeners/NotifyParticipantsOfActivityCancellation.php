<?php

namespace App\Listeners;

use App\Contracts\Notification\PushNotificationInterface;
use App\Contracts\User\UserInterface;
use App\Events\ActivityCancelled;
use App\Models\Enum\ActivityInvitationStatus;
use App\Models\Enum\NotificationTypes;
use App\Services\Mail\UserMailer;
use Carbon\Carbon;

class NotifyParticipantsOfActivityCancellation
{
    private $mail;
    private $notificationService;
    private $userService;

    public function __construct(UserMailer $mail, UserInterface $userService, PushNotificationInterface $notificationService){
        $this->mail = $mail;
        $this->notificationService = $notificationService;
        $this->userService = $userService;
    }

    /**
     * Handle the event.
     *
     * @param  ActivityCancelled  $event
     * @return void
     */
    public function handle(ActivityCancelled $event)
    {
        foreach ($event->activity->users as $user)
        {
            // if not owner, notify
            if ($user->id != $event->activity->owner_id) {

                // send notification to the users who have decided to join
                if ($user->pivot->status == ActivityInvitationStatus::JOINING){
                    $message = "{$event->activity->title} has been cancelled";

                    $this->notificationService->sendActivityNotification(
                        $user,
                        $event->activity,
                        NotificationTypes::CANCEL_ACTIVITY, $message
                    );
                }

            }
        }
    }
}
