<?php

namespace App\Contracts\Notification;

use App\Models\Eloquent\Activity\Activity;
use App\Models\Eloquent\User\User;

interface PushNotificationInterface
{
    public function sendActivityNotification(User $sendTo, Activity $activity, $notificationType, $message);

    public function sendContactNotification(User $sendTo, User $sendFrom, $notificationType, $message);
}