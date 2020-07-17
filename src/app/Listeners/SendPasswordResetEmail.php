<?php

namespace App\Listeners;

use App\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetEmail
{

    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  PasswordReset  $event
     * @return void
     */
    public function handle(PasswordReset $event)
    {
        $view = 'emails.user.passwordReset';
        $data = [
            'first_name' => $event->user->first_name,
            'password' => $event->newPassword,
        ];
        Mail::send($view, $data, function ($message) use ($event) {
            $message->to($event->user->email, $event->user->first_name)
                ->subject('New password: Back on track!');
        });
    }
}
