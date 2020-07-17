<?php

namespace App\Contracts\User;


use App\Models\Eloquent\Notification\UserDevice;

interface UserDeviceInterface
{
    public function save(UserDevice $user);

    public function findByUserId($userId);
}