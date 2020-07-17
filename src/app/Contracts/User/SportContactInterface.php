<?php

namespace App\Contracts\User;

use App\Models\Eloquent\User\User as Eloquent;
use Illuminate\Http\Request;

interface SportContactInterface
{

    public function findFriends($userId);

    public function delete($selfId, $sportContactId);

}