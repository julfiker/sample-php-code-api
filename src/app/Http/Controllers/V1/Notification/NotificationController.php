<?php

namespace App\Http\Controllers\V1\Notification;

use App\Contracts\Notification\NotificationInterface;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    private $notificationService;
    public function __construct(NotificationInterface $notificationService) {
        $this->notificationService = $notificationService;
    }

    public function getList($userId, Request $request)
    {
        return response()->json(
            $this->notificationService->findOfUser($userId, $request->input('status')),
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    public function markAsRead($id)
    {
        $this->notificationService->markAsRead($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
