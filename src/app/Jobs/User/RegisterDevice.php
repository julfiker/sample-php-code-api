<?php

namespace App\Jobs\User;

use App\Contracts\User\UserDeviceInterface;
use App\Contracts\User\UserInterface as UserContract;
use App\Jobs\Job;
use App\Models\Eloquent\Notification\UserDevice;
use App\Models\Eloquent\User\User;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Event;

class RegisterDevice extends Job implements SelfHandling
{

    public $user_id;
    public $device_id;
    public $platform;
    public $os;

    /**
     * Create a new job instance.
     *
     * @param $user_id
     * @param $device_id
     * @param $platform
     * @param $os
     */
    public function __construct($user_id, $device_id, $platform, $os)
    {
        $this->user_id = $user_id;
        $this->device_id = $device_id;
        $this->platform = $platform;
        $this->os = $os;
    }

    /**
     * Execute the job.
     *
     * @param User $user
     * @param UserContract $repo
     *
     * @return User $user
     */
    public function handle(UserDevice $userDevice, UserDeviceInterface $repo)
    {
        $userDevice->user_id = $this->user_id;
        $userDevice->device_id = $this->device_id;
        $userDevice->platform = $this->platform;
        $userDevice->os = $this->os;
        $user = $repo->save($userDevice);

        // TODO Fire UserSignedUp event
        //Event::fire(new UserSignedUp($user));

        return $user;
    }
}
