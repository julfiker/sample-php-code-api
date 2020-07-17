<?php  namespace App\Repositories\User; 

use App\Contracts\User\UserDeviceInterface;
use App\Models\Eloquent\Notification\UserDevice;

class UserDeviceRepository implements UserDeviceInterface {

    private $userDevice;
    public function __construct(UserDevice $userDevice){
        $this->userDevice = $userDevice;
    }

    public function save(UserDevice $device)
    {
        // remove registration for the same device by other users first
        $this->userDevice
            ->where('user_id', '!=', $device->user_id)
            ->where('device_id', $device->device_id)
            ->delete();

        $existingDevice = $this->userDevice
            ->where('user_id', $device->user_id)
            ->where('device_id', $device->device_id)
            ->first();

        if ($existingDevice != null) {
            return $existingDevice;
        } else {
            $device->save();
            return $device;
        }
    }

    public function findByUserId($userId){
        return $this->userDevice
            ->where('user_id', $userId)
            ->get();
    }
}