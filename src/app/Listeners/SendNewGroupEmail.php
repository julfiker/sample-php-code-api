<?php

namespace App\Listeners;

use App\Events\GroupCreateEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendNewGroupEmail
{

    /**
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  GroupCreateEvent  $event
     * @return void
     */
    public function handle(GroupCreateEvent $event)
    {
        $view = 'emails.group.createNew';

        Mail::send($view, ['admin' => env('MAIL_FROM_NAME'), 'event' => $event], function ($message) use ($event) {
            $message->to(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                ->subject('User has created new group');
        });
    }
}

