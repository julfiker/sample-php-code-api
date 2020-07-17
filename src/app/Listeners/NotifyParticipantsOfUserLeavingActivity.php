<?php

namespace App\Listeners;

use App\Events\UserLeftActivity;
use App\Services\Mail\UserMailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyParticipantsOfUserLeavingActivity
{

    /**
     * @var UserMailer
     */
    private $mail;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserMailer $mail)
    {
        //
        $this->mail = $mail;
    }

    /**
     * Handle the event.
     *
     * @param  UserLeftActivity  $event
     * @return void
     */
    public function handle(UserLeftActivity $event)
    {
        foreach ($event->activity->users() as $user)
        {
            //$this->mail->userLeftActivity($event->activity, $user);
        }
    }
}
