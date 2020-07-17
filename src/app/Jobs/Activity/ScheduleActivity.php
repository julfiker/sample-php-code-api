<?php

namespace App\Jobs\Activity;

use App\Contracts\Group\GroupInterface;
use App\Contracts\Activity\ActivityInterface as ActivityContract;
use App\Events\UserCreatedActivity;
use App\Exceptions\ValidationFailedException;
use App\Jobs\Job;
use App\Models\Eloquent\Activity\Activity;
use Illuminate\Auth\Guard;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class ScheduleActivity extends Job implements SelfHandling
{

    private $title;
    private $owner_id;
    private $sport_id;
    private $start_time;
    private $end_time;
    private $description;
    private $recurring;
    private $privacy;
    private $max_participants;
    private $long;
    private $lat;
    private $country;
    private $country_code;
    private $address;
    private $city;
    private $hotspot_id;
    private $group_id;

    /**
     * Create a new job instance.
     *
     * @param $title
     * @param $sport_id
     * @param $start_time
     * @param $end_time
     * @param $description
     * @param $recurring
     * @param $privacy
     * @param $max_participants
     * @param $long
     * @param $lat
     * @param $country
     * @param null $country_code
     * @param null $address
     * @param $city
     * @param null $invite_users
     * @param $hotspot_id
     */
    public function __construct($title, $sport_id, $start_time, $end_time, $description, $recurring, $privacy, $max_participants, $long, $lat, $country = null, $country_code = null, $address = null, $city = null, $invite_users = null, $hotspot_id = '', $group_id = null)
    {
        $this->title = $title;
        $this->owner_id = Auth::user()->id;
        $this->sport_id = $sport_id;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->description = $description;
        $this->recurring = $recurring;
        $this->privacy = $privacy;
        $this->max_participants = $max_participants;
        $this->long = $long;
        $this->lat = $lat;
        $this->country = $country;
        $this->country_code = $country_code;
        $this->address = $address;
        $this->city = $city;
        $this->invite_users = $invite_users;
        $this->hotspot_id = $hotspot_id;
        $this->group_id = $group_id;
    }

    /**
     * Execute the job.
     *
     * @param ActivityContract $repo
     * @param Activity $activity
     *
     * @return Activity
     */
    public function handle(ActivityContract $repo, Activity $activity, GroupInterface  $groupRepo, Guard $auth)
    {


        //Validation if group is private and not joined, then user can't create activity
        if ($this->group_id) {
            $group = $groupRepo->find($this->group_id);
            if ($group &&  $group->is_private) {
                if(!$auth->user()->groups->contains($this->group_id))
                {
                    throw new ValidationFailedException("The group is private, You cannot create activity. You have to join the group.");
                }
            }
        }

        $activity = $repo->save($activity->fill(get_object_vars($this)));
        if ($this->invite_users !== null) {
            $userIds = [];
            foreach($this->invite_users as $user) {
                $userIds[] = $user['id'];
            }
            $repo->usersInvited($activity->id, $userIds);
        }

        Event::fire(new UserCreatedActivity($activity));

        return $activity;
    }
}
