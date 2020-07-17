<?php

namespace App\Jobs\Sport;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use App\Contracts\Sport\SportInterface;
use App\Models\Eloquent\Lists\Sport as SportEloquent;
use App\Exceptions\ValidationFailedException;

class  SportDeleteJob extends Job implements SelfHandling
{
    public $id;
    protected $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $user_id)
    {
        $this->id = $id;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SportEloquent $sportEloquent)
    {
        $sport = $sportEloquent->find($this->id);

        //fixme: Entity by user checking should be done by query instead of condition applied after sql executed.
        if ($sport && $sport->user_id == $this->user_id)
            return $sport->delete();

        throw new ValidationFailedException("Invalid entity request!");
    }
}
