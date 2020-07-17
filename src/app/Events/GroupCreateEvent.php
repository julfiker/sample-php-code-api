<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GroupCreateEvent extends Event
{
    use SerializesModels;

    public $group;

    /**
     * Create group
     * @param $group
     */
    public function __construct($group)
    {
        $this->group = $group;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
