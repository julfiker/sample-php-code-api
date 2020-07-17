<?php  namespace App\Services\Mail; 

use Illuminate\Support\Facades\Mail;

class Mailer {

    public function sendTo($sendTo, $subject, $view, $data = [])
    {
        Mail::send($view, $data, function($message) use ($sendTo, $subject)
        {
            $message->to($sendTo)
                ->subject($subject);

        });
    }
}