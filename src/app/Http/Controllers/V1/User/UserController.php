<?php

namespace App\Http\Controllers\V1\User;

use App\Contracts\User\UserInterface as UserContract;
use App\Events\UserSignedUp;
use App\Exceptions\ValidationFailedException;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UserChangePasswordRequest;
use App\Http\Requests\User\UserSignUpRequest;
use App\Jobs\User\UserSignsUp;
use App\Jobs\User\UserUpdatesProfile;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * Illuminate's Auth Contract implementation
     *
     * @var Guard
     */
    private $auth;

    /**
     * User Contract implementation
     *
     * @var UserContract
     */
    private $user;


    /**
     * @param UserContract $user
     * @param Guard $auth
     */
    public function __construct(UserContract $user, Guard $auth)
    {
        $this->user = $user;
        $this->auth = $auth;
    }

    /**
     * Return the currently logged in user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(
            ['data' => $this->auth->user()],
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    /**
     * Return user by ID
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function view($id)
    {
        $loggedInUserId = $this->auth->user()->id;
        $user = $this->user->findWithRelationshipStatusAndStatistics($id, $loggedInUserId);

        return response()->json(
            ['data' => $user],
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    /**
     * Create a new user.
     *
     * Request body will be validated in UserSignUpRequest
     * and passed on to the UserSignsUp job for further
     * handling of the user sign up process.
     *
     * @param UserSignUpRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(UserSignUpRequest $request)
    {

        $user = $this->dispatchFrom(UserSignsUp::class, $request);
        return response()->json(
            ['data' => $user],
            Response::HTTP_CREATED,
            [],
            JSON_NUMERIC_CHECK
        );

    }

    /**
     * Update a user's profile.
     *
     * Request body will be validated in UpdateProfileRequest
     * and passed on to the UserUpdatesProfile job for further
     * handling of the profile update process.
     *
     * @param UpdateProfileRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProfileRequest $request)
    {

        $this->dispatchFrom(UserUpdatesProfile::class, $request);
        return response()->json(null, Response::HTTP_NO_CONTENT);

    }

    /**
     * Delete a user.
     *
     * Whether hard or soft deletes depends on the existence
     * of the $data property on the user's model holding the 'deleted_at'
     * value.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function delete()
    {
        $this->user->disableAccount($this->auth->user()->id);
        return response(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * Search users
     *
     * @param $searchTerm urlencoded search string
     * @return mixed
     * @throws ValidationFailedException
     */
    public function search($searchTerm, Request $request)
    {
        // decode the urlencoded search string
        $searchTerm =  trim(urldecode($searchTerm));

        if (strlen($searchTerm)<3) {
            throw new ValidationFailedException('Oops. Your search should be at least three characters long.');
        }
        return response()->json(
            $this->user->search($searchTerm, $this->auth->user()->id, $request)
        );
    }

}
