<?php

namespace App\Jobs\Group;

use App\Contracts\User\UserInterface;
use App\Events\GroupCreateEvent;
use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
Use App\Models\Eloquent\Group\Group;
Use App\Contracts\Group\GroupInterface;

class GroupLeaveJob extends Job implements SelfHandling
{
    private $groupId;
    private $userId;

    /**
     * Join a group.
     * @param string $name
     * @param int $user_id
     */
    public function __construct($groupId,$userId)
    {
        $this->groupId = $groupId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Group $group, GroupInterface $repo, UserInterface $userRepo)
    {
        $group = $repo->find($this->groupId);
        $user = $userRepo->find($this->userId);

        if ($group && $user) {
            $repo->leaveFromGroup($group, $user);
            //\Event::fire(new GroupJoinedEvent($group));
            return $group;
        }

        throw new ValidationFailedException("Invalid leave request!");
    }
}
