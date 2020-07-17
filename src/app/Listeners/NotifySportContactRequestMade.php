<?php

namespace App\Listeners;

use App\Contracts\Notification\PushNotificationInterface;
use App\Contracts\User\UserInterface;
use App\Events\SportContactRequestMade;
use App\Models\Enum\NotificationTypes;
use App\Services\Mail\UserMailer;

class NotifySportContactRequestMade
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
     * @param  SportContactRequestMade  $event
     * @return void
     */
    public function handle(SportContactRequestMade $event)
    {
        $format = "%s %s sent you a buddy request";
        $message = sprintf(
            $format,
            $event->from->first_name,
            $event->from->last_name
        );

        $this->notificationService->sendContactNotification($event->to, $event->from, NotificationTypes::CONNECTION_REQUEST_RECEIVED, $message);
        //$this->mail->sportContactRequestReceived($event->from, $event->to);
    }
}
