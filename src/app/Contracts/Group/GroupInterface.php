<?php

namespace App\Contracts\Group;

use App\Models\Eloquent\Group\Group;
use App\Models\Eloquent\User\User;
use Illuminate\Http\Request;

interface GroupInterface
{
    public function findMyAllGroups();
    public function save(Group $group);
    public function find($id);
    public function delete($id);
    public function joinAsMember(Group $group, User $user);
    public function leaveFromGroup(Group $group, User $user);
    public function joinRequest(Group $group, User $user);
}