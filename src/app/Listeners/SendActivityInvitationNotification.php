<?php

namespace App\Listeners;

use App\Contracts\Notification\PushNotificationInterface;
use App\Contracts\User\UserInterface;
use App\Events\UsersInvitedToActivity;
use App\Models\Enum\NotificationTypes;
use App\Services\Mail\UserMailer;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendActivityInvitationNotification
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
     * @param  UsersInvitedToActivity  $event
     * @return void
     */
    public function handle(UsersInvitedToActivity $event)
    {
        $owner = $this->userService->find($event->activity->owner_id);

        foreach ($event->users as $userId)
        {
            $user = $this->userService->find($userId);
            //$this->mail->invitedToActivity($event->activity, $user);

            $message = "{$owner->first_name} invited you to {$event->activity->title}";
            $this->notificationService->sendActivityNotification($user, $event->activity, NotificationTypes::INVITE_TO_ACTIVITY, $message);
        }
    }
}
