<?php

namespace App\Http\Controllers\V1\Group;

use App\Contracts\Activity\ActivityInterface;
use App\Exceptions\ValidationFailedException;
use App\Jobs\Group\GroupCreateJob;
use App\Jobs\Group\GroupJoinJob;
use App\Jobs\Group\GroupJoinRequestJob;
use App\Jobs\Group\GroupLeaveJob;
use App\Jobs\Group\GroupUpdateJob;
use App\Jobs\Sport\GroupDeleteJob;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Guard;
use Illuminate\Http\Response;

use App\Contracts\Group\GroupInterface;

//model
use App\Models\Eloquent\Group\Group;

class GroupsController extends Controller
{
    private $groupRepo;
    private $auth;

    /** @var \App\Contracts\Activity\ActivityInterface  */
    private $activity;

    public function __construct(GroupInterface $groupInterface, Guard $auth, ActivityInterface $activity) {
        $this->groupRepo = $groupInterface;
        $this->auth = $auth;
        $this->activity = $activity;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->groupRepo->findMyAllGroups();

        return response()->json(
            ['data' => $data],
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
        
    }

    /**
     * Joining action to join in a group for user
     */
    public function joinGroup($groupId, Request $request)
    {
        $group = $this->dispatchFrom(GroupJoinJob::class, $request, array('groupId' => $groupId, 'userId' => $this->auth->user()->id));
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Leave action to leave from a group
     */
    public function leaveGroup($groupId, Request $request)
    {
        $group = $this->dispatchFrom(GroupLeaveJob::class, $request, array('groupId' => $groupId, 'userId' => $this->auth->user()->id));
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Leave action to leave from a group
     */
    public function joinRequestGroup($groupId, Request $request)
    {
        $group = $this->dispatchFrom(GroupJoinRequestJob::class, $request, array('groupId' => $groupId, 'userId' => $this->auth->user()->id));
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Guard $auth)
    {
        $this->validate($request, array(
                'name' => 'required'
            )
        );
        $group = $this->dispatchFrom(GroupCreateJob::class, $request, ['user_id' => $auth->user()->id]);
        return response()->json(
            ['data' => $group],
            Response::HTTP_CREATED,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = new Group();
        $data = $group->findOrFail($id);

        return response()->json(
            ['data' => $data],
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->dispatchFrom(GroupUpdateJob::class, $request, array('id' => $id, 'user_id' => $this->auth->user()->id));
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($this->activity->countActivityByGroupId($id))
        {
            throw new ValidationFailedException("You cannot delete group ID ".$id.", it use on activity.");
        }

        if($this->auth->user()->groups()->contains($id))
        {
            throw new ValidationFailedException("You cannot delete group ID ".$id.", One or more user already joined this group.");
        }

        $this->dispatchFrom(GroupDeleteJob::class, collect(array('id' => $id)), ['user_id' => $this->auth->user()->id]);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
