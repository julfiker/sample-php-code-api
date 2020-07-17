<?php

namespace App\Contracts\User;

use App\Models\Eloquent\User\User as Eloquent;
use Illuminate\Http\Request;

interface SportContactRequestInterface
{

    public function make($requestedToUserId, $currentUserId);
    public function accept($requestingUserId, $currentUserId);
    public function decline($requestingUserId, $currentUserId);
    public function cancel($requestingUserId, $currentUserId);

}