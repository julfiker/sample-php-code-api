<?php

namespace App\Contracts\User;

use App\Models\Eloquent\User\User as Eloquent;
use Illuminate\Http\Request;

interface UserInterface
{

    public function save(Eloquent $user);

    public function find($id);

    public function findWithRelationshipStatusAndStatistics($targetUserId, $currentUserId);

    public function findByEmail($email);

    public function isExistsByEmail($email);

    public function search($searchTerm, $userIdToSkipToSkip, $request);

    public function disableAccount($id);

    public function sync(Eloquent $user, $relation, $values = [], $remove = true);

    public function isConnected($fromUserId, $toUserId);

    public function isRequestPending($fromUserId, $toUserId);

    public function isRequestDeclined($fromUserId, $toUserId);

    public function connect($contactUserId, $selfId);

    public function changePassword($userId, $newPassword);

    public function resetPassword($user);

}