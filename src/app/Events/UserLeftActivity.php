<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Eloquent\Activity\Activity;
use App\Models\Eloquent\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserLeftActivity extends Event
{
    use SerializesModels;
    /**
     * @var Activity
     */
    public $activity;
    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Activity $activity, User $user)
    {
        $this->activity = $activity;
        $this->user = $user;
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
