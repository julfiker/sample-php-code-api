<?php

namespace App\Jobs\Sport;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use App\Contracts\Group\GroupInterface;
use App\Models\Eloquent\Group\Group;
use App\Exceptions\ValidationFailedException;

class  GroupDeleteJob extends Job implements SelfHandling
{
    public $id;
    protected $user_id;

    /**
     * Create a new job instance.
     * @param int $id
     * @param int $user_id
     */
    public function __construct($id, $user_id)
    {
        $this->id = $id;
        $this->user_id = $user_id;
    }

    /**
     * Execute job to delete a group
     * @param Group $group
     * @throws
     */
    public function handle(Group $group)
    {
        $group = $group->find($this->id);

        //fixme: Entity by user checking should be done by query instead of condition applied after sql executed.
        if ($group && $group->user_id == $this->user_id)
            return $group->delete();

        throw new ValidationFailedException("Invalid request for delete a group!");
    }
}
