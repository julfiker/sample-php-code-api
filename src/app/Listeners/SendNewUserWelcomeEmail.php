<?php

namespace App\Listeners;

use App\Events\UserSignedUp;
use App\Services\Mail\UserMailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendNewUserWelcomeEmail
{

    /**
     * @var Mailer
     */
    private $mail;

    /**
     * Create the event listener.
     *
     * @param Mailer $mail
     */
    public function __construct(UserMailer $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Handle the event.
     *
     * @param  UserSignedUp  $event
     * @return void
     */
    public function handle(UserSignedUp $event)
    {
        $view = 'emails.user.welcomeNewUser';

        Mail::send($view, ['first_name' => $event->user->first_name], function ($message) use ($event) {
            $message->to($event->user->email, $event->user->first_name)
                ->subject('Lets bring Spoly to life!');
        });
    }
}
