<?php

namespace App\Contracts\Notification;

use App\Models\Eloquent\Notification\Notification;
use App\Models\Eloquent\User\User;

interface NotificationInterface
{
    public function findOfUser($userId, $status = null);

    public function markAsRead($id);

    public function create(User $sendTo, $referenceUser, $referenceActivity, $notificationType, $message);

    public function save(Notification $notification);

    public function updateStatus(Notification $notification, $newStatus, $errorMessage = null);

    public function isAlreadyInvited($activityId, $userId);
}