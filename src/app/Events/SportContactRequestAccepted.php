<?php

namespace App\Events;

use App\Models\Eloquent\User\User;
use Illuminate\Queue\SerializesModels;

class SportContactRequestAccepted extends Event
{
    use SerializesModels;
    /**
     * @var User
     */
    public $acceptedBy;
    /**
     * @var User
     */
    public $requestedBy;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $acceptedBy, User $requestedBy)
    {
        $this->acceptedBy = $acceptedBy;
        $this->requestedBy = $requestedBy;
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
