<?php

namespace App\Listeners;

use App\Events\PasswordChanged;
use App\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;

class SendPasswordChangedEmail
{

    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param PasswordChanged|PasswordReset $event
     */
    public function handle(PasswordChanged $event)
    {
        $view = 'emails.user.passwordChanged';
        $data = [
            'first_name' => $event->user->first_name,
            'password' => $event->newPassword,
        ];
        Mail::send($view, $data, function ($message) use ($event) {
            $message->to($event->user->email, $event->user->first_name)
                ->subject('Password changed: Good to go!');
        });
    }
}
