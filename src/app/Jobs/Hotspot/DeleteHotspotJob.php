<?php

namespace App\Jobs\Hotspot;

use App\Events\HotspotCreated;
use App\Jobs\Job;
use App\Models\Eloquent\Hotspot\Hotspot;
use App\Repositories\Hotspot\HotspotRepository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Event;
use App\Exceptions\ValidationFailedException;

class DeleteHotspotJob extends Job implements SelfHandling
{
    protected $id;
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
     * @param Hotspot $hotspot
     * @param HotspotRepository $repository
     * @return Hotspot
     */
    public function handle(Hotspot $hotspot)
    {
        $hotspotEntry = $hotspot->find($this->id);
        echo $this->user_id;die();
        //fixme: Entity by user checking should be done by query instead of condition applied after sql executed.
        if ($hotspotEntry && $hotspotEntry->user_id == $this->user_id)
            return $hotspotEntry->delete();

        throw new ValidationFailedException("Invalid request!");
    }
}
