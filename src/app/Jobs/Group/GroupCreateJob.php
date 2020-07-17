<?php

namespace App\Jobs\Group;

use App\Events\GroupCreateEvent;
use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
Use App\Models\Eloquent\Group\Group;
Use App\Contracts\Group\GroupInterface;

class GroupCreateJob extends Job implements SelfHandling
{
    public $name;
    public $user_id;
    public $is_private;

    /**
     * Create a new job instance.
     * @param string $name
     * @param int $user_id
     */
    public function __construct($name, $is_private, $user_id)
    {
        $this->name = $name;
        $this->user_id = $user_id;
        $this->is_private = $is_private;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Group $group, GroupInterface $repo)
    {
        $input = $group->fill(get_object_vars($this));
        $group = $repo->save($input);
        \Event::fire(new GroupCreateEvent($group));

        return $group;
    }
}
