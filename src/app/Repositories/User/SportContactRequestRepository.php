<?php namespace App\Repositories\User;


use App\Contracts\User\SportContactRequestInterface;
use App\Models\Eloquent\Notification\SportContactRequest;
use App\Models\Enum\ContactRequestStatus;

class SportContactRequestRepository implements SportContactRequestInterface
{

    /**
     * @var SportContact
     */
    private $contactRequest;

    public function __construct(SportContactRequest $request)
    {
        $this->contactRequest = $request;
    }

    public function make($requestingUserId, $currentUserId)
    {
        $request = $this->contactRequest->makeOrWake($requestingUserId, $currentUserId);
        $request->save();
        return $request;
    }

    public function accept($requestingUserId, $currentUserId)
    {
        $request = $this->contactRequest
                ->where('from', $requestingUserId)
                ->where('to', $currentUserId)
                ->where('status', ContactRequestStatus::PENDING)
                ->firstOrFail();
        $request->accept($currentUserId);
        $request->save();
        return $request;
    }

    public function decline($requestingUserId, $currentUserId)
    {
        $request = $this->contactRequest
            ->where('from', $requestingUserId)
            ->where('to', $currentUserId)
            ->where('status', ContactRequestStatus::PENDING)
            ->firstOrFail();
        $request->decline($currentUserId);
        $request->save();
    }

    public function cancel($requestingUserId, $currentUserId)
    {
        $request = $this->contactRequest
            ->where('from', $currentUserId)
            ->where('to', $requestingUserId)
            ->where('status', ContactRequestStatus::PENDING)
            ->firstOrFail();
        // change the status first
        $request->cancel($currentUserId);
        $request->save();

        // delete now
        $request->delete();
        return $request;
    }
}