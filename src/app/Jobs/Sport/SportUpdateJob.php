<?php

namespace App\Jobs\Sport;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use App\Contracts\Sport\SportInterface;
use App\Models\Eloquent\Lists\Sport as SportEloquent;

class SportUpdateJob extends Job implements SelfHandling
{
    public $id;
    public $name;
    public $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id = null, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SportEloquent $sportEloquent, SportInterface $repo)
    {
        $sport = $sportEloquent->find($this->id);
        //fixme: Entity by user checking should be done by query instead of condition applied after sql executed.
        if ($sport && $sport->user_id == $this->user_id) {
            $sport->name = $this->name;
            $repo->save($sport);
            return $sport;
        }
        throw new ValidationFailedException("Invalid request!");
    }
}
