<?php

namespace App\Repositories\Activity;

use App\Contracts\Activity\ActivityInterface;
use App\Exceptions\ValidationFailedException;
use App\Models\Eloquent\Activity\Activity;
use App\Models\Eloquent\User\User;
use App\Models\Enum\ActivityInvitationStatus;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class ActivityRepository implements ActivityInterface  {

    public function __construct(Activity $activityEloquent, User $userEloquent)
    {
        $this->activityEloquent = $activityEloquent;
        $this->userEloquent = $userEloquent;
        $this->pageSize = Config::get('constants.page_size');
    }

    /**
     * Find an activity
     *
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->activityEloquent
            ->findOrFail($id);
    }

    /**
     * Find an activity of an owner
     *
     * @param $id
     * @param $userId
     * @return mixed
     */
    public function findOfUser($id, $userId)
    {
        return $this->activityEloquent
            ->where('owner_id', $userId)
            ->findOrFail($id);
    }

    /**
     * Returns own activities filer by month and year
     *
     * @param $userId
     * @param $month
     * @param $year
     * @return mixed
     */
    public function findUserActivities($userId, $month, $year)
    {
        $query = $this->activityEloquent
            ->where('owner_id', $userId)
            ->orderBy('start_time', 'asc');

        if (!empty($month) && !empty($year)) {
            $month = intval($month);
            $year = intval($year);
            $startDate = date('Y-m-d 00:00:00', strtotime("{$year}-{$month}-01"));
            $endDate = date('Y-m-t 23:59:59', strtotime($startDate));
            $query = $query->whereBetween('start_time', [$startDate, $endDate]);
        }

        return $query->paginate($this->pageSize);
    }

    /**
     * Returns other user's activities where the user  has already joined in
     * Filtered by month and year
     *
     * @param $userId
     * @param $month
     * @param $year
     * @return mixed
     */
    public function findJoinedActivitiesByInvitation($userId, $month, $year)
    {
        $query = $this->activityEloquent
            ->where('owner_id', '!=' , $userId)
            ->whereIn('id',function($query) use ($userId){
                $query
                    ->select('activity_id')
                    ->from('pivot_activity_user')
                    ->where('user_id', $userId)
                    ->where('status', ActivityInvitationStatus::JOINING)
                ;
            });

        if (!empty($month) && !empty($year)) {
            $month = intval($month);
            $year = intval($year);
            $startDate = "{$year}-{$month}-01 00:00:00";
            $endDate = date('Y-m-t 23:59:59', strtotime($startDate));
            $query = $query->whereBetween('start_time', [$startDate, $endDate]);
        }

        return $query->paginate($this->pageSize);
    }


    /**
     * Returns list of future events where the user has joined in
     * Does not return the events that are already past
     * That would include own activities too.
     *
     * @param $userId
     * @return mixed
     */
    public function getFutureJoinedActivities($userId)
    {
        $query = $this->activityEloquent
            ->whereIn('id',function($query) use ($userId){
                $query
                    ->select('activity_id')
                    ->from('pivot_activity_user')
                    ->where('user_id', $userId)
                    ->where('status', ActivityInvitationStatus::JOINING)
                ;
            })
            ->where('end_time','>=', date('Y-m-d H:i:s'))
            ->orderBy('start_time');

        return $query->paginate($this->pageSize);
    }

    /**
     * Returns other user's activities where the user has already been invited
     *
     * @param $userId
     * @param $month
     * @param $year
     * @return mixed
     */
    public function findInvitedActivities($userId, $month, $year)
    {
        $query = $this->activityEloquent
            ->where('owner_id', '!=' , $userId)
            ->whereIn('id',function($query) use ($userId){
                $query
                    ->select('activity_id')
                    ->from('pivot_activity_user')
                    ->where('user_id', $userId)
                    ->where('status', ActivityInvitationStatus::INVITED)
                ;
            });

        if (!empty($month) && !empty($year)) {
            $month = intval($month);
            $year = intval($year);
            $startDate = "{$year}-{$month}-01 00:00:00";
            $endDate = date('Y-m-t 23:59:59', strtotime($startDate));
            $query = $query->whereBetween('start_time', [$startDate, $endDate]);
        }

        return $query->paginate($this->pageSize);
    }

    /**
     * Returns list of future events where the user is invited to
     * Does not return the events that are already past
     * That would include own activities too.
     *
     * @param $userId
     * @return mixed
     */
    public function getFutureInvitedActivities($userId)
    {
        $query = $this->activityEloquent
            ->whereIn('id',function($query) use ($userId){
                $query
                    ->select('activity_id')
                    ->from('pivot_activity_user')
                    ->where('user_id', $userId)
                    ->where('status', ActivityInvitationStatus::INVITED)
                ;
            })
            ->where('end_time','>=', date('Y-m-d H:i:s'))
            ->orderBy('start_time');

        return $query->paginate($this->pageSize);
    }

    /**
     * Save an activity
     *
     * @param Activity $activity
     * @return Activity
     */
    public function save(Activity $activity)
    {
        $activity->save();
        $owner =  $activity->users()->find($activity->owner_id);

        if ($owner === null) {
            // user is not already added as a participant, so add him
            $this->activityEloquent
                ->findOrFail($activity->id)
                ->users()
                ->attach(
                    $activity->owner_id,
                    ['status' => ActivityInvitationStatus::JOINING]
                );
        }

        return $activity;
    }

    /**
     * Return list of matching activities to explore
     *
     * Matching criteria:
     * 1. not the owner
     * 2. country is matched
     * 3. city is matched
     * 4. is an 'open' event
     * 5. not over yet
     * 6. sport is matched
     * 7. max participant limit has not been reached yet
     * 8. not already a participant
     *
     * @param $userId
     * @return mixed
     * @throws ValidationFailedException
     */
    public function explore($userId)
    {
        $user = $this->userEloquent->findOrFail($userId);
        $sportIds = [];
        foreach($user->sports as $sport) {
            $sportIds[] = $sport->id;
        }

        $hotspotIds = [];
        foreach($user->hotspots as $hotspot)
        {
            $hotspotIds[] = $hotspot->id;
        }

        if (count($sportIds) == 0) {
            throw new ValidationFailedException("Oops. You did not add a sport in your profile yet.");
        }

        $data = $this->activityEloquent
            ->select('activity.*')
            ->where('owner_id', '!=', $userId)
            ->where('country', '=', $user->current_country)
            //->where('city', '=', $user->current_city) //Commented this since not mandatory
            ->where('privacy', '=', 'open')
            ->where('end_time', '>=', Carbon::now())
            ->whereIn('sport_id', $sportIds)
           //->whereIn('hotspot_id', $hotspotIds)  //Commented this since not mandatory
            ->where('max_participants','>', function($query){
                $query
                    ->select(DB::raw('count(*)'))
                    ->from('pivot_activity_user')
                    ->whereRaw('activity_id = activity.id')
                    ->where('status', ActivityInvitationStatus::JOINING)
                ;
            })
            ->whereNotIn('id',function($query) use ($userId){
                $query
                    ->select('activity_id')
                    ->from('pivot_activity_user')
                    ->where('user_id', $userId)
                ;
           })
            //->paginate($this->pageSize);
            // fixme: return to default page size when FE is fixed to handle that.
            ->paginate(500); // show all -- keep a limit though

        return $data;
    }


    /**
     * Search all activities that are not over yet and can be joined     *
     * Filtered by sport, country and city (all optional)
     *
     * @param null $sportId
     * @param null $country
     * @param null $city
     * @return mixed
     */
    public function search($sportId = null, $country = null, $city = null)
    {

        /**
         * Matching criteria:
         * 1. is an 'open' event
         * 2. not over yet
         * 3. max participant limit has not been reached yet
         */
        $query = $this->activityEloquent
            ->select('activity.*')
            ->where('privacy', '=', 'open')
            ->where('end_time', '>=', date('Y-m-d H:i:s'))
            ->where('max_participants','>', function($query){
                $query
                    ->select(DB::raw('count(*)'))
                    ->from('pivot_activity_user')
                    ->whereRaw('activity_id = activity.id')
                    ->where('status', ActivityInvitationStatus::JOINING)
                ;
            });

        if (!empty($sportId)) {
            $query = $query->where('sport_id', (int) $sportId);
        }

        if (!empty($country)) {
            $query = $query->where('country', $country);
        }

        if (!empty($city)) {
            $query = $query->where('city', $city);
        }

        return $query->paginate($this->pageSize);
    }

    /**
     * Cancel/remove/delete an activity
     *
     * @param $id
     * @return mixed
     */
    public function cancel($id)
    {
        $activity = $this->activityEloquent->findOrFail($id);
        $activity->delete();
        return $activity;
    }

    /**
     * User joins to an activity
     *
     * @param $activityId
     * @param $joiningUserId
     * @return mixed
     * @throws ValidationFailedException
     */
    public function userJoins($activityId, $joiningUserId)
    {
        $activity = $this->activityEloquent->findOrFail($activityId);
        $joiningUser = $activity->users()->find($joiningUserId);

        //participants list contains all the users with different status.
        // Counting the actual participants who agreed to join
        $joiningUserCount =0;
        foreach($activity->participants as $user) {
            if ($user->pivot->status == ActivityInvitationStatus::JOINING) {
                $joiningUserCount++;
            }
        }

        if ($joiningUserCount >= $activity->max_participants) {
            throw new ValidationFailedException("Bummer. The activity has reached its max of joining buddies.");
        }

        if ($joiningUser != null) {
            if ($joiningUser->pivot->status == ActivityInvitationStatus::LEFT) {
                throw new ValidationFailedException("Bummer. You cannot rejoin because you deleted this activity.");
            }
            $this->updateStatus($activityId, $joiningUserId, ActivityInvitationStatus::JOINING);
        } else {
            if ($activity->privacy != 'open')
                throw new ValidationFailedException("Bummer. You cannot join a private activity without an invitation.");
            $activity
                ->users()
                ->attach(array_fill_keys([$joiningUserId], ['status' => ActivityInvitationStatus::JOINING]));
        }

        return $activity->fresh();
    }

    /**
     * User leaves an activity
     *
     * @param $activityId
     * @param $leavingUserId
     * @return mixed
     */
    public function userLeaves($activityId, $leavingUserId)
    {
        return $this->updateStatus($activityId, $leavingUserId, ActivityInvitationStatus::LEFT);
    }

    /**
     * Invites users to an activity
     *
     * @param $activityId
     * @param array $userIDs
     * @return mixed
     */
    public function usersInvited($activityId, array $userIDs)
    {
        $activity = $this->activityEloquent->findOrFail($activityId);

        // TODO: make it efficient
        foreach($activity->users as $user) {
            $key = array_search($user->id, $userIDs);
            if ($key !== false) {
                // already invited
                unset($userIDs[$key]);
            }
        }

        $activity->users()
            ->attach(array_fill_keys($userIDs, ['status' => ActivityInvitationStatus::INVITED]));

        return $activity;
    }

    /**
     * User declines to join an activity invitation
     * @param $id
     * @param $decliningUserId
     * @return mixed
     */
    public function userDeclines($id, $decliningUserId)
    {
        return $this->updateStatus($id, $decliningUserId, ActivityInvitationStatus::DECLINED);
    }

    /**
     * Update participation status of an user to an activity
     *
     * @param $activityId
     * @param $userId
     * @param $status
     * @return mixed
     */
    private function updateStatus($activityId, $userId, $status) {
        $activity = $this->activityEloquent
            ->findOrFail($activityId);
        $pivot = $activity
            ->users()
            ->findOrFail($userId)
            ->pivot;
        $pivot->status = $status;
        $pivot->save();

        return $activity;
    }

    /**
     * Return count of own activities
     *
     * @param $userId
     * @return mixed
     */
    public function countOwnActivity($userId)
    {
        return $this->activityEloquent
            ->where('owner_id', $userId)
            ->count();
    }

    /**
     * Return count of joined activities
     *
     * @param $userId
     * @return mixed
     */
    public function countJoinedActivity($userId)
    {
        return $this->activityEloquent
            ->whereIn('id',function($query) use ($userId){
                $query
                    ->select('activity_id')
                    ->from('pivot_activity_user')
                    ->where('user_id', $userId)
                    ->where('status', ActivityInvitationStatus::JOINING)
                ;
            })->count();
    }

    public function countActivityBySportId($sportId)
    {
        return $this->activityEloquent
            ->where('sport_id', $sportId)
            ->count();
    }

    public function countActivityByGroupId($groupId) {
        return $this->activityEloquent
            ->where('group_id', $groupId)
            ->count();
    }

    public function updateSport($source, $target)
    {
        return $this->activityEloquent
            ->where('sport_id', $source)
            ->update(['sport_id' => $target]);
    }
}
