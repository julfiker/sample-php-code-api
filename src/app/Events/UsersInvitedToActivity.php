<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Eloquent\Activity\Activity;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UsersInvitedToActivity extends Event
{
    use SerializesModels;
    /**
     * @var Activity
     */
    public $activity;
    /**
     * @var array
     */
    public $users;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Activity $activity, array $users)
    {
        $this->activity = $activity;
        $this->users = $users;
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
