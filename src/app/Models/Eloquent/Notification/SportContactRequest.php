<?php

namespace App\Models\Eloquent\Notification;

use App\Models\Eloquent\BaseModel;
use app\Models\Enum\ContactRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Exception\HttpResponseException;
use Symfony\Component\HttpFoundation\JsonResponse;

class SportContactRequest extends BaseModel
{

    use SoftDeletes;

    protected $table = 'sport_contact_request';
    protected $dates = ['deleted_at'];

    /**
     * If current user is the recipient of the request and the request
     * can be accepted; accept it.
     *
     * @param $currentUserId
     *
     * @return $this
     */
    public function accept($currentUserId)
    {
        if ($currentUserId != $this->to || $this->status != ContactRequestStatus::PENDING)
        {
            $this->notAllowedToException('accept');
        }

        $this->setStatus(ContactRequestStatus::ACCEPTED);
        return $this;
    }

    /**
     * If current user is the recipient of the request and the request
     * can be declined; decline it.
     *
     * @param $currentUserId
     *
     * @return $this
     */
    public function decline($currentUserId)
    {

        if ($currentUserId != $this->to || $this->status != ContactRequestStatus::PENDING)
        {
            $this->notAllowedToException('decline');
        }

        $this->setStatus(ContactRequestStatus::DECLINED);
        return $this;

    }

    /**
     * If current user is the sender of the request and the request
     * can be cancelled; cancel it. Also soft delete the request so
     * it will no longer be returned in other queries.
     *
     * @param $currentUserId
     *
     * @return $this
     * @throws \Exception
     */
    public function cancel($currentUserId)
    {

        if ($currentUserId != $this->from || $this->status != ContactRequestStatus::PENDING)
        {
            $this->notAllowedToException('cancel');
        }

        $this->setStatus(ContactRequestStatus::CANCELLED);
        return $this;

    }

    /**
     * Set the request's status to reset and soft delete the request.
     *
     * @return $this
     * @throws \Exception
     */
    public function reset()
    {
        $this->setStatus(ContactRequestStatus::RESET);
        $this->delete();
        return $this;
    }

    /**
     * Set the status of the request to the one provided.
     * A validation will be performed to check if the status conforms to
     * the statuses defined as a class constant.
     *
     * @param $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        if (static::validateStatus($status))
        {
            $this->status = $status;
            return $this;
        }

        $this->inValidStatusException();

    }

    /**
     * Throw this exception when a user is not allowed to update the status.
     *
     * @param $action
     */
    protected function notAllowedToException($action)
    {
        $this->httpResponseException('You are not allowed to ' . $action . ' this request.', 403);
    }

    /**
     * Throw this exception when the request is updated with a status that
     * has not be defined as a class constant.
     *
     */
    protected function inValidStatusException()
    {
        $this->httpResponseException('Tried to update request status with invalid status', 500);
    }

    /**
     * Throw this exception when the request already has a certain status.
     *
     * @param $status
     */
    protected function requestIsAlreadyStatusException($status)
    {
        $this->httpResponseException('This request is already ' . $status, 409);
    }

    /**
     * Throw a new Http Response exception using a Json Response
     * made with the provided message and statuscode.
     *
     * @param $message
     * @param $statusCode
     */
    protected function httpResponseException($message, $statusCode)
    {
        throw new HttpResponseException(
            $this->jsonHttpResponseExceptionFormat(
                $message,
                $statusCode
        ));
    }

    /**
     * Make a new Json Response using the provided message and
     * statuscode.
     *
     * @param $message
     * @param $statusCode
     *
     * @return JsonResponse
     */
    protected function jsonHttpResponseExceptionFormat($message, $statusCode)
    {
        return new JsonResponse([
            'rootMessage' => $message
        ], $statusCode);
    }

    /**
     * Validate if the status that is currently being set on the request
     * exists as a class constant.
     *
     * @param $status
     *
     * @return bool
     */
    public static function validateStatus($status)
    {
        // TODO Create validation of status (must be defined as a constant on top of the model)
        return true;
    }

    /**
     * Make a new request with status pending or wake and old request
     * that was soft deleted and/or had its status set to declined/cancelled/reset.
     *
     * @param $userId
     * @param $currentUserId
     *
     * @return SportContactRequest|static
     */
    public static function makeOrWake($userId, $currentUserId)
    {

        // Get previously existing request (soft deleted or not) or instantiate
        // a new request.
        if (!$request = static::findByUsersWithTrashed($userId, $currentUserId))
        {
            $request = new static();
        }

//        // Check if status can be set to pending if the request previously existed on the DB
//        // and is not already accepted or pending else throw an exception.
//        if ($request->exists && !in_array($request->status, [
//                ContactRequestStatus::DECLINED,
//                ContactRequestStatus::CANCELLED,
//                ContactRequestStatus::RESET
//            ]))
//        {
//            $request->requestIsAlreadyStatusException($request->status);
//        }

        // Set values "from" & "to" (also in case the receiver is now the sender)
        $request->from = (int) $currentUserId;
        $request->to = (int) $userId;
        $request->status = ContactRequestStatus::PENDING;

        // Set deleted_at to null in case the request was soft deleted.
        $request->deleted_at = null;

        return $request;

    }


    /**
     * Find an existing request based on user ID's excluding
     * soft deleted requests.
     *
     * @param $userId
     * @param $currentUserId
     *
     * @return SportContactRequest|null
     */
    public static function findByUsers($userId, $currentUserId)
    {
        $data = [$userId, $currentUserId];
        return static::whereIn('from', $data)->whereIn('to', $data)->first();
    }

    /**
     * Find an existing request based on user ID's including
     * soft deleted requests.
     *
     * @param $userId
     * @param $currentUserId
     *
     * @return SportContactRequest|null
     */
    public static function findByUsersWithTrashed($userId, $currentUserId)
    {
        $data = [$userId, $currentUserId];
        return static::withTrashed()->whereIn('from', $data)->whereIn('to', $data)->first();
    }

}
