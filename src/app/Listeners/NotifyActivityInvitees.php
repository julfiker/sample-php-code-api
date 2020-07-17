<?php

namespace App\Listeners;

use App\Contracts\Notification\PushNotificationInterface;
use App\Events\UserCreatedActivity;
use App\Models\Eloquent\Activity\Activity;
use App\Models\Enum\NotificationTypes;
use App\Services\Mail\UserMailer;
use Illuminate\Support\Facades\Auth;

class NotifyActivityInvitees
{

    /**
     * @var UserMailer
     */
    private $mail;

    /**
     * @var $notification
     */
    private $notification;

    /**
     * Create the event listener.
     * @param UserMailer $mail
     * @param PushNotificationInterface $notification
     */
    public function __construct(UserMailer $mail, PushNotificationInterface $notification)
    {
        $this->mail = $mail;
        $this->notification = $notification;
    }

    /**
     * Handle the event.
     *
     * @param  UserCreatedActivity  $event
     * @return void
     */
    public function handle(UserCreatedActivity $event)
    {
        $event->activity->load('users');
        foreach ($event->activity->users as $user)
        {
            // if not owner, notify
            if ($user->id != $event->activity->owner_id) {
                $this->notification->sendActivityNotification(
                    $user,
                    $event->activity,
                    NotificationTypes::INVITE_TO_ACTIVITY,
                    $this->constructMessage($event->activity)
                );
            }
        }
    }

    private function constructMessage(Activity $activity) {
        $currentUser = Auth::user();
        return "{$currentUser->first_name} invited you to {$activity->title}";
    }
}
