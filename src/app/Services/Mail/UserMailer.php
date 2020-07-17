<?php  namespace App\Services\Mail; 

use App\Models\Eloquent\Activity\Activity;
use App\Models\Eloquent\User\User;

class UserMailer extends Mailer {

    public function welcome(User $user)
    {
        // TODO CREATE MAILER VIEWS
        $view = 'emails.user.welcomeNewUser';
        $data = ['user' => $user];
        $subject = 'Hi! Welcome to Spoly!';
        return $this->sendTo($user, $subject, $view, $data);
    }

    public function sportContactRequestReceived(User $requester, User $requestee)
    {
        $view = 'emails.VIEWHERE';
        $data = [];
        $subject = '.....';
        return $this->sendTo($requestee, $subject, $view, $data);
    }

    public function invitedToActivity(User $user, Activity $activity)
    {
        $view = 'emails.VIEWHERE';
        $data = [];
        $subject = '.....';
        return $this->sendTo($user, $subject, $view, $data);
    }

    public function activityCancelled(Activity $activity)
    {
        $view = 'emails.VIEWHERE';
        $data = [];
        $subject = '.........';
        return $this->sendTo($user, $subject, $view, $data);
    }

    public function userLeftActivity(Activity $activity)
    {
        $view = 'emails.VIEWHERE';
        $data = [];
        $subject = '.........';
        return $this->sendTo($user, $subject, $view, $data);
    }

}