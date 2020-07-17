<?php

namespace App\Events;

use App\Models\Eloquent\Activity\Activity;
use Illuminate\Queue\SerializesModels;

class ActivityUpdated extends Event
{
    use SerializesModels;
    /**
     * @var Activity
     */
    public $activity;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
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
