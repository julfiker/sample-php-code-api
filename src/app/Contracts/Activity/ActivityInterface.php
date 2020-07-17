<?php

namespace App\Contracts\Activity;

use App\Http\Requests\Request;
use App\Models\Eloquent\Activity\Activity as Eloquent;

interface ActivityInterface
{

    public function find($id);

    public function findOfUser($id, $userId);

    public function findUserActivities($userId, $month, $year);

    public function findJoinedActivitiesByInvitation($userId, $month, $year);

    public function getFutureJoinedActivities($userId);

    public function findInvitedActivities($userId, $month, $year);

    public function getFutureInvitedActivities($userId);

    public function countOwnActivity($userId);

    public function countJoinedActivity($userId);

    public function save(Eloquent $activity);

    public function explore($userId);

    public function search($sportId = null, $country = null, $city = null);

    public function userJoins($activityId, $joiningUserId);

    public function userDeclines($id, $decliningUserId);

    public function userLeaves($activityId, $leavingUserId);

    public function usersInvited($activityId, array $userIDs);

    public function cancel($id);

    public function updateSport($source, $target);

}