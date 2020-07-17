<?php

namespace App\Listeners;

use App\Events\SportCreateEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendNewSportEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SportCreateEvent  $event
     * @return void
     */
    public function handle(SportCreateEvent $event)
    {
        $view = 'emails.sport.createNewSport';

        Mail::send($view, ['admin' => env('MAIL_FROM_NAME'), 'event' => $event], function ($message) use ($event) {
            $message->to(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                ->subject('User has created new sport');
        });
    }
}

