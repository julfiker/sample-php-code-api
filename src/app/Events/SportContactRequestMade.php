<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Eloquent\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SportContactRequestMade extends Event
{
    use SerializesModels;
    /**
     * @var User
     */
    public $from;
    /**
     * @var User
     */
    public $to;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
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
