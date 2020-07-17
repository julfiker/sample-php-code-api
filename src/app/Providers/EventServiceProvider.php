<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

        // User Signup
        'App\Events\UserSignedUp' => [
            'App\Listeners\SendNewUserWelcomeEmail',
        ],

        // Sport-contact request made
        'App\Events\SportContactRequestMade' => [
            'App\Listeners\NotifySportContactRequestMade',
        ],

        // Activities was created
        'App\Events\UserCreatedActivity' => [
            'App\Listeners\NotifyActivityInvitees'
        ],

        // Activity was updated
        'App\Events\ActivityUpdated' => [
            'App\Listeners\NotifyParticipantsOfActivityUpdate'
        ],

        // Users invited to activity
        'App\Events\UsersInvitedToActivity' => [
            'App\Listeners\SendActivityInvitationNotification'
        ],

        // Activity was cancelled
        'App\Events\ActivityCancelled' => [
            'App\Listeners\NotifyParticipantsOfActivityCancellation'
        ],

        // User joined activity
        'App\Events\UserJoinedActivity' => [
            'App\Listeners\NotifyOwnerOfUserJoiningActivity'
        ],

        // User declined joining activity
        'App\Events\UserDeclinedActivity' => [
            'App\Listeners\NotifyOwnerOfUserDeclinedJoiningActivity'
        ],

        // User left activity
        'App\Events\UserLeftActivity' => [
            'App\Listeners\NotifyParticipantsOfUserLeavingActivity'
        ],

        // Password reset
        'App\Events\PasswordReset' => [
            'App\Listeners\SendPasswordResetEmail'
        ],

        // Password changed
        'App\Events\PasswordChanged' => [
            'App\Listeners\SendPasswordChangedEmail'
        ],

        // Sport create
        'App\Events\SportCreateEvent' => [
            'App\Listeners\SendNewSportEmail'
        ],

        //Group
        'App\Events\GroupCreateEvent' => [
            'App\Listeners\SendNewGroupEmail'
        ],

    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
