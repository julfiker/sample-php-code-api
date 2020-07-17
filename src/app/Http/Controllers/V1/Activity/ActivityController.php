<?php

namespace App\Http\Controllers\V1\Activity;

use App\Contracts\Activity\ActivityInterface;
use App\Events\ActivityCancelled;
use App\Events\ActivityUpdated;
use App\Events\UserDeclinedActivity;
use App\Events\UserJoinedActivity;
use App\Events\UserLeftActivity;
use App\Events\UsersInvitedToActivity;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\Activity\CreateActivityRequest;
use App\Http\Requests\Activity\UpdateActivityRequest;
use App\Jobs\Activity\ScheduleActivity;
use App\Jobs\Activity\UpdateActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class ActivityController extends Controller
{

    // TODO Handle recurring activities? (daily, weekly, monthly) Ask for help!
    private $activity;
    private $currentUser;

    public function __construct(ActivityInterface $activity)
    {
        $this->activity = $activity;
        $this->currentUser = Auth::user();
    }

    /**
     * Get list of activities user has created
     * @param $userId
     * @param Request $request
     * @return mixed
     */
    public function getOwnActivities($userId, Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        return response()->json(
            $this->activity->findUserActivities($userId, $month, $year),
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    /**
     * Gets list of activities user was invited by other other and has already joined
     * @param $userId
     * @param Request $request
     * @return mixed
     */
    public function getJoinedActivitiesByInvitation($userId, Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        return response()->json(
            $this->activity->findJoinedActivitiesByInvitation($userId, $month, $year),
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get list of future events where the user has already joined in
     * Does not return events that are already past
     * That would include own activities too.
     *
     * @param $userId
     */
    public function getFutureJoinedActivities($userId)
    {
        return response()->json(
            $this->activity->getFutureJoinedActivities($userId),
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    /**
     * Gets list of activities user got invited to, but not joined or declined yet.
     * @param $userId
     * @param Request $request
     * @return mixed
     */
    public function getInvitedActivities($userId, Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        return response()->json(
            $this->activity->findInvitedActivities($userId, $month, $year),
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get list of future events where the user is invited to
     * Does not return the events that are already past
     * That would include own activities too.
     *
     * @param $userId
     */
    public function getFutureInvitedActivities($userId)
    {
        return response()->json(
            $this->activity->getFutureInvitedActivities($userId),
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    public function view($id)
    {
        return response()->json([
            'data' => $this->activity->find($id)
        ], Response::HTTP_OK, [], JSON_NUMERIC_CHECK);
    }

    public function create(CreateActivityRequest $request)
    {
        // TODO 1. Validate Date and time not before now.

        return response()->json([
            'data' => $this->dispatchFrom(ScheduleActivity::class, $request),
        ], Response::HTTP_CREATED, [], JSON_NUMERIC_CHECK);

    }

    public function update(UpdateActivityRequest $request)
    {
        // create a log channel
        $log = new Logger('Activity');
        $log->pushHandler(new StreamHandler('../storage/logs/activity.log', Logger::WARNING));

        $log->warning(json_encode(get_object_vars ($request)));

        $activity = $this->dispatchFrom(UpdateActivity::class, $request);
        Event::fire(new ActivityUpdated($activity));
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Returns the activities that are to be explored by the current user
     * @return mixed
     */
    public function explore()
    {
        // TODO Get a good spatial search query
        // Example from google developers: SELECT id, ( 3959 * acos( cos( radians(37) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(-122) ) + sin( radians(37) ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < 25 ORDER BY distance LIMIT 0 , 20;
        // Full article here: https://developers.google.com/maps/articles/phpsqlsearch_v3#findnearsql

        return response()->json(
            $this->activity->explore($this->currentUser->id),
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    /**
     * Search among the activities based on the specified sport_id, country and city
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        $sportId = $request->input('sport_id');
        $country = trim($request->input('country'));
        $city = trim($request->input('city'));

        return response()->json(
            $this->activity->search($sportId, $country, $city),
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    public function delete($id)
    {
        $activity = $this->activity->cancel($id);
        Event::fire(new ActivityCancelled($activity));
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function join($id)
    {
        $activity = $this->activity->userJoins($id, $this->currentUser->id);
        Event::fire(new UserJoinedActivity($activity, Auth::user()));
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function decline($id)
    {
        $activity = $this->activity->userDeclines($id, $this->currentUser->id);
        Event::fire(new UserDeclinedActivity($activity, Auth::user()));
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function leave($id)
    {
        $activity = $this->activity->userLeaves($id, $this->currentUser->id);
        Event::fire(new UserLeftActivity($activity, Auth::user()));
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function invite(Request $request, $id)
    {
        $activity = $this->activity->usersInvited($id, $request->users);
        Event::fire(new UsersInvitedToActivity($activity, $request->users));
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
