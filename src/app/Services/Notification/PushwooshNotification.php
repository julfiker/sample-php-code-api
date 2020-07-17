<?php  namespace App\Services\Notification;

use App\Contracts\Notification\PushNotificationInterface;
use App\Contracts\User\UserDeviceInterface;
use App\Models\Eloquent\Activity\Activity;
use App\Models\Eloquent\User\User;
use App\Models\Enum\NotificationStatus;
use Gomoob\Pushwoosh\Model\Notification\Notification;
use Gomoob\Pushwoosh\Model\Notification\Platform;
use Gomoob\Pushwoosh\Model\Request\CreateMessageRequest;
use App\Models\Eloquent\Notification\Notification as NotificationEloquent;
use League\Flysystem\Exception;
use Schimpanz\Pushwoosh\PushwooshManager;

class PushwooshNotification implements PushNotificationInterface {

    private $pushwoos;
    private $userDevice;
    public function __construct(PushwooshManager $pushwoos, UserDeviceInterface $userDevice)
    {
        $this->pushwoos = $pushwoos;
        $this->userDevice = $userDevice;
    }

    /**
     * Add notification and send a push notification
     * @param User $sendTo
     * @param Activity $activity
     * @param $notificationType
     * @param $message
     */
    public function sendActivityNotification(User $sendTo, Activity $activity, $notificationType, $message)
    {
        $this->sendNotification($sendTo, null, $activity, $notificationType, $message);
    }

    public function sendContactNotification(User $sendTo, User $sendFrom, $notificationType, $message) {
        $this->sendNotification($sendTo, $sendFrom, null, $notificationType, $message);
    }

    private function sendNotification(User $sendTo, $referenceUser, $referenceActivity, $notificationType, $message){
        $notification = $this->saveNotification($sendTo, $referenceUser, $referenceActivity, $notificationType, $message);

        $userDevices = $this->userDevice->findByUserId($sendTo->id);

        // do not try to send push notification if no user device is found
        if (count($userDevices) == 0)
            return;

        // Create a new notification.
        $pushNotification = Notification::create()->setContent($message);

        foreach($userDevices as $userDevice) {

            if (strcasecmp($userDevice->platform, 'android') === 0)
                $pushNotification->addPlatform(Platform::android());
            else if (strcasecmp($userDevice->platform, 'ios') === 0)
                $pushNotification->addPlatform(Platform::iOS());
            else
                continue; // platform not supported

            $pushNotification->addDevice($userDevice->device_id);
        }

        $data = [
            'id' => $notification->id,
            'type' => $notificationType,
        ];

        if ($referenceUser !== null) {
            $data['reference_user_id'] = $referenceUser->id;
        }

        if ($referenceActivity !== null) {
            $data['reference_activity_id'] = $referenceActivity->id;
        }

        $pushNotification->setData($data);

        // Create a request for the '/createMessage' web service.
        $request = CreateMessageRequest::create()->addNotification($pushNotification);

        // Send out the notification.
        try{
            $response = $this->pushwoos->createMessage($request);

            if($response->isOk()) {
                $this->changeNotificationStatus($notification, NotificationStatus::SENT);
            } else {
                // could not send
                $this->changeNotificationStatus($notification,
                    NotificationStatus::FAILED_TO_SEND,
                    $response->getStatusCode() . " : " . $response->getStatusMessage()
                );
            }
        } catch(Exception $e){
            // something went wrong
            $this->changeNotificationStatus($notification,
                NotificationStatus::FAILED_TO_SEND,
                $e->getMessage()
            );
        }
    }

    private function saveNotification(User $sendTo, $referenceUser, $referenceActivity, $notificationType, $message){
        $notification = new NotificationEloquent();
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

    private function changeNotificationStatus(NotificationEloquent $notification, $newStatus, $errorMessage = null){
        $notification->status = $newStatus;
        $notification->error = $errorMessage;
        $notification->save();
        return $notification;
    }
}