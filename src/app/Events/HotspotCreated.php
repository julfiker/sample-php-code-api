<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class HotspotCreated extends Event
{
    use SerializesModels;

    public $hotspot;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($hotspot)
    {
        $this->hotspot = $hotspot;
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
