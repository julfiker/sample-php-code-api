<?php

namespace App\Http\Controllers\V1\User;

use App\Contracts\User\SportContactInterface;
use App\Contracts\User\SportContactRequestInterface;

use Illuminate\Support\Facades\Event;
use App\Events\SportContactRequestAccepted;
use App\Events\SportContactRequestMade;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Contracts\User\UserInterface as UserContract;

class SportContactController extends Controller
{

    private $sportContactRequestContract;
    private $userContract;

    public function __construct(
        UserContract $userContract, SportContactRequestInterface $sportContactRequestContract,
        SportContactInterface $sportContact
    )
    {
        $this->userContract = $userContract;
        $this->sportContactRequestContract = $sportContactRequestContract;
        $this->sportContact = $sportContact;
        $this->currentUser = Auth::user();
    }

    public function getFriendList($userId)
    {
        return response()->json(
            ['data' =>$this->sportContact->findFriends($userId)]
        );
    }

    public function invite($requestedToUserId)
    {

        // TODO Extract to service
        if ($this->userContract->isConnected($this->currentUser->id, $requestedToUserId))
        {
            return response()->json(
                ['rootMessage' => 'Already connected with this user.'],
                Response::HTTP_CONFLICT
            );
        }
        elseif ($this->userContract->isRequestPending($this->currentUser->id, $requestedToUserId))
        {
            return response()->json(
                ['rootMessage' => 'Request already pending.'],
                Response::HTTP_CONFLICT
            );
        }
        $this->sportContactRequestContract->make($requestedToUserId, $this->currentUser->id);
        Event::fire(new SportContactRequestMade($this->currentUser, $this->userContract->find($requestedToUserId)));
        return response()->json(null,  Response::HTTP_CREATED);
    }

    public function accept($fromId)
    {
        $this->sportContactRequestContract->accept($fromId, $this->currentUser->id);
        $this->userContract->connect($fromId, $this->currentUser->id);
        Event::fire(new SportContactRequestAccepted($this->currentUser, $this->userContract->find($fromId)));
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function decline($fromId)
    {
        $this->sportContactRequestContract->decline($fromId, $this->currentUser->id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function cancelFriendRequest($toId)
    {
        $this->sportContactRequestContract->cancel($toId, $this->currentUser->id);
        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function unfriend($userId)
    {
        $this->sportContact->delete($this->currentUser->id, $userId);
        return response()->json([], Response::HTTP_NO_CONTENT);
    }


}
