<?php  namespace App\Repositories\Notification;

use App\Contracts\Notification\NotificationInterface;
use App\Models\Eloquent\Notification\Notification;
use App\Models\Eloquent\User\User;
use App\Models\Enum\NotificationStatus;
use App\Models\Enum\NotificationTypes;
use Illuminate\Support\Facades\Config;

class NotificationRepository implements NotificationInterface {

    private $notificationEloquent;
    public function __construct(Notification $notificationEloquent){
        $this->notificationEloquent = $notificationEloquent;
    }

    public function findOfUser($userId, $status = null) {
        $pageSize = Config::get('constants.page_size');
        $query = $this->notificationEloquent
            ->where('send_to', $userId)
            ->orderBy('id', 'DESC');

        if (!empty($status)) {
            if ($status == "READ") {
                $query->where('status', NotificationStatus::READ);
            } else if ($status == "UNREAD"){
                $query->whereIn('status', [
                    NotificationStatus::NEW_NOTIFICATION,
                    NotificationStatus::SENT,
                    NotificationStatus::FAILED_TO_SEND
                ]);
            } else {
                $query->where('status', $status);
            }
        }

        return $query->paginate($pageSize);
    }

    public function save(Notification $notification)
    {
        $notification->save();
        return $notification;
    }

    public function markAsRead($id){
        $notification = $this->notificationEloquent->findOrFail($id);
        $notification->status = NotificationStatus::READ;
        $notification->save();
        return $notification;
    }

    public function updateStatus(Notification $notification, $newStatus, $errorMessage = null)
    {
        $notification->status = $newStatus;
        $notification->error = $errorMessage;
        $notification->save();
        return $notification;
    }

    public function create(User $sendTo, $referenceUser, $referenceActivity, $notificationType, $message){
        $notification = new Notification();
        $notification->send_to = $sendTo->id;
        if ($referenceUser !== null)
            $notification->reference_user_id = $referenceUser->id;
        if ($referenceActivity !== null)
            $notification->reference_activity_id = $referenceActivity->id;

        $notification->type = $notificationType;
        $notification->message = $message;
        $notification->status = NotificationStatus::NEW_NOTIFICATION;
        $notification->save();
        return $notification;
    }

    public function isAlreadyInvited($activityId, $userId)
    {
        $notifications = $this->notificationEloquent
            ->where('send_to','=', $userId)
            ->where('reference_activity_id','=', $activityId)
            ->where('type','=', NotificationTypes::INVITE_TO_ACTIVITY)
            ->get();

        if (count($notifications)>0)
            return true;
        else
            return false;
    }
}