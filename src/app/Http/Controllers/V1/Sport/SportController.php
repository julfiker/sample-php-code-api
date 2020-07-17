<?php

namespace App\Http\Controllers\V1\Sport;

use App\Contracts\Activity\ActivityInterface;
use App\Exceptions\ValidationFailedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Http\Requests\Sport\SportDestroyRequest;
use App\Http\Requests\Sport\SportMergeRequest;
use App\Http\Requests\Sport\SportRequest;
use App\Http\Requests\Sport\SportUpdateRequest;
use App\Jobs\Sport\SportCreateJob;
use App\Jobs\Sport\SportDeleteJob;
use App\Jobs\Sport\SportMergeJob;
use App\Jobs\Sport\SportUpdateJob;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Response;

class SportController extends Controller
{
    public $activity;
    public $auth;

    public function __construct(ActivityInterface $activity, Guard $auth)
    {
        $this->activity = $activity;
        $this->auth = $auth;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(SportRequest $request,  Guard $auth)
    {
        $sport = $this->dispatchFrom(SportCreateJob::class, $request, ['user_id' => $auth->user()->id]);
        return response()->json(
            ['data' => $sport],
            Response::HTTP_CREATED,
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
    public function update(SportUpdateRequest $request, $id)
    {
        $this->dispatchFrom(SportUpdateJob::class, $request, array('id' => $id, 'user_id' => $auth->user()->id));
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

        if($this->activity->countActivityBySportId($id))
        {
            throw new ValidationFailedException("You cannot delete sport ID ".$id.", it use on activity.");
        }

        if(in_array($id, $this->auth->user()->sports->toArray()))
        {
            throw new ValidationFailedException("You cannot delete sport ID ".$id.", it use on user.");
        }

        $this->dispatchFrom(SportDeleteJob::class, collect(array('id' => $id)), ['user_id' => $this->auth->user()->id]);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Merge sport id
     * @param SportMergeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function merge(SportMergeRequest $request)
    {
        $this->dispatchFrom(SportMergeJob::class, $request);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
