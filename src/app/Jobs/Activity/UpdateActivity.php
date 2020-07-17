<?php

namespace App\Jobs\Activity;

use App\Contracts\Activity\ActivityInterface as ActivityContract;
use App\Jobs\Job;
use App\Models\Eloquent\Activity\Activity;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Event;
use Mockery\Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class UpdateActivity extends Job implements SelfHandling
{
    private $id;
    private $title;
    private $sport_id;
    private $start_time;
    private $end_time;
    private $description;
    private $recurring;
    private $privacy;
    private $max_participants;
    private $lat;
    private $long;
    private $street;
    private $street_number;
    private $address;
    private $city;
    private $country;
    private $country_code;
    private $participants;
    private $hotspot_id;
    private $group_id;

    /**
     * Create a new job instance.
     *
     * @param $id
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
     * @param $street
     * @param $street_number
     * @param null $address
     * @param null $city
     * @param null $country
     * @param null $country_code
     * @param null $participants
     * @param $hotspot_id
     */
    public function __construct($id, $title, $sport_id = null, $start_time = null, $end_time = null, $description = null, $recurring = null, $privacy = null, $max_participants = null, $long = null, $lat = null, $street = '', $street_number = '', $country = null, $country_code = null, $city = null, $address = null, $participants = null, $hotspot_id = "", $group_id = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->sport_id = $sport_id;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->description = $description;
        $this->recurring = $recurring;
        $this->privacy = $privacy;
        $this->max_participants = $max_participants;
        $this->lat = $lat;
        $this->long = $long;
        $this->street = $street;
        $this->street_number = $street_number;
        $this->address = $address;
        $this->city = $city;
        $this->country = $country;
        $this->country_code = $country_code;
        $this->participants = $participants;
        $this->hotspot_id = $hotspot_id;
        $this->group_id = $group_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ActivityContract $repo, Activity $activityModel)
    {

        // create a log channel
//        $log = new Logger('Activity');
//        $log->pushHandler(new StreamHandler('logs/activity.log', Logger::WARNING));
//
//        $log->warning(json_encode(get_object_vars ($this)));

        $activity = $repo->find($this->id);
        if ($this->title !== null) {
            $activity->title = $this->title;
        }
        if ($this->sport_id !== null) {
            $activity->sport_id = $this->sport_id;
        }
        if ($this->start_time !== null) {
            $activity->start_time = $this->start_time;
        }
        if ($this->end_time !== null) {
            $activity->end_time = $this->end_time;
        }
        if ($this->description !== null) {
            $activity->description = $this->description;
        }
        if ($this->recurring !== null) {
            $activity->recurring = $this->recurring;
        }
        if ($this->max_participants !== null) {
            $activity->max_participants = $this->max_participants;
        }
        if ($this->lat !== null) {
            $activity->lat = $this->lat;
        }
        if ($this->long !== null) {
            $activity->long = $this->long;
        }
        if ($this->street !== '') {
            $activity->street = $this->street;
        }
        if ($this->street_number !== '') {
            $activity->street_number = $this->street_number;
        }
        if ($this->city !== null) {
            $activity->city = $this->city;
        }
        if ($this->country !== null) {
            $activity->country = $this->country;
        }
        if ($this->country_code !== null) {
            $activity->country_code = $this->country_code;
        }
        if ($this->address !== null) {
            $activity->address = $this->address;
        }

        // User can select hotspot mode or manual mode.
        // If user select manual mode we must set hotspot_id to zero.
        if ($this->hotspot_id !== null) {
            $activity->hotspot_id = $this->hotspot_id;
        }else{
            $activity->hotspot_id = 0;
        }

        $repo->save($activity);

        if ($this->participants !== null) {
            $userIds = [];
            foreach($this->participants as $user) {
                $userIds[] = $user['id'];
            }
            $repo->usersInvited($activity->id, $userIds);
        }


        return $activity;
    }
}
