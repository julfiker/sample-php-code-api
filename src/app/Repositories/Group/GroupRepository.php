<?php namespace App\Repositories\Group;

use App\Contracts\Group\GroupInterface;
use App\Models\Eloquent\Group\Group;
use App\Models\Eloquent\User\User;
use Illuminate\Auth\Guard;
use Illuminate\Support\Facades\DB;
use App\Models\Enum\GroupMemberStatus;

class GroupRepository implements GroupInterface
{


    private $group;
    private $auth;

    public function __construct(Group $group, Guard $auth)
    {
        $this->group = $group;
        $this->auth = $auth;
    }

    /**
     * Save the sport model to the database
     * @param Group $group
     * @return Group
     */
    public function save(Group $group)
    {
        $group->save();
        return $group;
    }

    public function find($id)
    {
        return $this->group->findOrFail($id);
    }

    /**
     * Find authenitcate user all groups
     */
    public function findMyAllGroups()
    {
        return $this->group
            ->where('user_id', '=', $this->auth->user()->id )
            ->orderBy('name', 'asc')
            ->get()
            ;
    }

    public function delete($id)
    {
        $entity = $this->group->find($id);

        if ($entity !== null) {
            return $entity->delete();
        }

        return;
    }

    /**
     * Add member with join status
     *
     * @param Group $group
     * @param User $user
     */
    public function joinAsMember(Group $group, User $user) {
        $userId = $user->id;
        $group->members()->sync([$userId => ['status' => GroupMemberStatus::GROUP_JOINED]]);
    }

    /**
     * Leave from group, not deleted, just only change status, so we can kept member information under the group
     *
     * @param Group $group
     * @param User $user
     */
    public function leaveFromGroup(Group $group, User $user) {
        $userId = $user->id;
        $group->members()->sync([$userId => ['status' => GroupMemberStatus::GROUP_LEAVED]]);
    }

    /**
     * Add member with join status
     *
     * @param Group $group
     * @param User $user
     */
    public function joinRequest(Group $group, User $user) {
        $userId = $user->id;
        $group->members()->sync([$userId => ['status' => GroupMemberStatus::GROUP_JOINREQUEST]]);
    }

}
