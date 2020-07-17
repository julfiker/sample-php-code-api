<?php

namespace App\Events;

use App\Models\Eloquent\User\User;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Event
{
    use SerializesModels;

    public $user;
    public $newPassword;

    public function __construct(User $user, $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
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
