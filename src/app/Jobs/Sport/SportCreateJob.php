<?php

namespace App\Jobs\Sport;

use App\Events\SportCreateEvent;
use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
Use App\Models\Eloquent\Lists\Sport;
Use App\Contracts\Sport\SportInterface;

class SportCreateJob extends Job implements SelfHandling
{
    public $name;
    public $user_id;

    /**
     * Create a new job instance.
     * 
     * @param $name
     * @param $user_id
     * @return void
     */
    public function __construct($name, $user_id)
    {
        $this->name = $name;
        $this->user_id = $user_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Sport $sport, SportInterface $repo)
    {
        $sport = $repo->save($sport->fill(get_object_vars($this)));
        \Event::fire(new SportCreateEvent($sport));

        return $sport;
    }
}
