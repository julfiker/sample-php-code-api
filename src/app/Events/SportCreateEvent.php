<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SportCreateEvent extends Event
{
    use SerializesModels;

    public $sport;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($sport)
    {
        $this->sport = $sport;
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
