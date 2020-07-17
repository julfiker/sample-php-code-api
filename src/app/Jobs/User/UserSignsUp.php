<?php

namespace App\Jobs\User;

use App\Contracts\User\UserInterface;
use App\Events\UserSignedUp;
use App\Jobs\Job;
use App\Models\Eloquent\User\User;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Event;

class UserSignsUp extends Job implements SelfHandling
{

    public $first_name;
    public $last_name;
    public $birthday;
    public $email;
    public $password;

    /**
     * Create a new job instance.
     *
     * @param $first_name
     * @param $last_name
     * @param $birthday
     * @param $email
     * @param $password
     */
    public function __construct($first_name, $last_name, $birthday, $email, $password)
    {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->birthday = $birthday;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Execute the job.
     * @param User $user
     * @param UserInterface $repo
     * @return User
     */
    public function handle(User $user, UserInterface $repo)
    {
        $user = $repo->save($user->fill(get_object_vars($this)));

        Event::fire(new UserSignedUp($user));
        return $user;
    }
}
