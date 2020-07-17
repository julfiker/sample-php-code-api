<?php

namespace App\Listeners;

use App\Contracts\Notification\PushNotificationInterface;
use App\Contracts\User\UserInterface;
use App\Events\SportContactRequestAccepted;
use App\Services\Mail\UserMailer;

class SendSportContactRequestAccepted
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
     * @param  SportContactRequestAccepted $event
     * @return void
     */
    public function handle(SportContactRequestAccepted $event)
    {
        $format = "%s %s your buddy request";
        $message = sprintf(
            $format,
            $event->acceptedBy->first_name,
            $event->acceptedBy->last_name
        );

        $this->notificationService->sendContactNotification($event->requestedBy, $event->acceptedBy, NotificationTypes::CONNECTION_REQUEST_RECEIVED, $message);
        //$this->mail->sportContactRequestReceived($event->from, $event->to);
    }
}
