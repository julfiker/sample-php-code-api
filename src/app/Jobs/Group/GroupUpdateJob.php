<?php

namespace App\Jobs\Group;

use App\Exceptions\ValidationFailedException;
use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use App\Contracts\Group\GroupInterface;
use App\Models\Eloquent\Group\Group;

class GroupUpdateJob extends Job implements SelfHandling
{
    public $id;
    public $name;
    public $user_id;

    /**
     * Create a new job instance.
    */
    public function __construct($id = null, $name, $user_id)
    {
        $this->id = $id;
        $this->name = $name;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     * @param Group $groupEloquent
     * @param GroupInterface $repo
     * @return Group
     * @throws
     */
    public function handle(Group $groupEloquent, GroupInterface $repo)
    {
        $group = $groupEloquent->find($this->id);
        //fixme: Entity by user checking should be done by query instead of condition applied after sql executed.
        if ($group && $group->user_id == $this->user_id) {
            $group->name = $this->name;
            $repo->save($group);
            return $group;
        }
        throw new ValidationFailedException("Invalid request to update a group!");
    }
}
