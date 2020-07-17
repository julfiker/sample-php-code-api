<?php

namespace App\Jobs\Sport;

use App\Contracts\Activity\ActivityInterface;
use App\Contracts\Sport\SportInterface;
use App\Contracts\User\UserInterface;
use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class SportMergeJob extends Job implements SelfHandling
{
    public $ids;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    /**
     * Execute the job.
     *
     * @param SportInterface $sport
     * @param ActivityInterface $activity
     * @param UserInterface $user
     * @return void
     */
    public function handle(SportInterface $sport, ActivityInterface $activity, UserInterface $user)
    {
        for($i=1; $i<count($this->ids); $i++)
        {
            // Update activity table
            $activity->updateSport($this->ids[$i], $this->ids[0]);

            // Update pivot_sport_user table
            $sport->updatePivotSportUser($this->ids[$i], $this->ids[0]);

            // Delete unused sport
            $sport->delete($this->ids[$i]);
        }

        return true;
    }
}
