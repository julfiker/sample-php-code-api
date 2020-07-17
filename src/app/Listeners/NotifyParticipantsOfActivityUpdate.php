<?php

namespace App\Listeners;

use App\Contracts\Notification\NotificationInterface;
use App\Contracts\Notification\PushNotificationInterface;
use App\Contracts\User\UserInterface;
use App\Events\ActivityUpdated;
use App\Models\Eloquent\Activity\Activity;
use App\Models\Enum\ActivityInvitationStatus;
use App\Models\Enum\NotificationTypes;
use App\Services\Mail\UserMailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NotifyParticipantsOfActivityUpdate
{

    private $mail;
    private $notificationService;
    private $userService;
    private $notificationRepo;

    public function __construct(UserMailer $mail, UserInterface $userService, NotificationInterface $notificationRepo, PushNotificationInterface $notificationService){
        $this->mail = $mail;
        $this->notificationService = $notificationService;
        $this->userService = $userService;
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * Handle the event.
     *
     * @param  ActivityUpdated  $event
     * @return void
     */
    public function handle(ActivityUpdated $event)
    {
        $event->activity->load('users');
        foreach ($event->activity->users as $user)
        {
            // if not owner, notify
            if ($user->id != $event->activity->owner_id) {

                if ($user->pivot->status == ActivityInvitationStatus::JOINING){
                    // already invited, so just update them about the change
                    $message = "{$event->activity->title} has been changed";
                    $this->notificationService->sendActivityNotification($user, $event->activity, NotificationTypes::ACTIVITY_UPDATED, $message);
                } else if (!$this->notificationRepo->isAlreadyInvited($event->activity->id, $user->id)) {
                    // newly invited. So, just send them event notification
                    $this->notificationService->sendActivityNotification(
                        $user,
                        $event->activity,
                        NotificationTypes::INVITE_TO_ACTIVITY,
                        $this->constructInvitationMessage($event->activity)
                    );
                }
            }
        }
    }

    private function constructInvitationMessage(Activity $activity) {
        $currentUser = Auth::user();
        return "{$currentUser->first_name} invited you to {$activity->title}";
    }
}
