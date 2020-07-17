<?php

namespace App\Http\Controllers\V1\Settings;
use App\Exceptions\ValidationFailedException;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Services\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Mockery\CountValidator\Exception;

class FeedbackController extends Controller
{
    /**
     * Send feedback emails
     *
     * @param Mailer $mailer
     * @param Request $request
     * @return mixed
     * @throws ValidationFailedException
     */
    public function send(Mailer $mailer, Request $request)
    {
        $currentUser = Auth::user();
        $fullName = $currentUser->first_name . ' ' . $currentUser->last_name;

        if ($request->type == 'bug') {
            $sendTo = Config::get('mail.feedback_for_bug');
            $subject = "Bug by {$fullName}";
        } elseif ($request->type == 'idea') {
            $sendTo = Config::get('mail.feedback_for_idea');
            $subject = "Idea by {$fullName}";
        } else {
            throw new ValidationFailedException("Oops. Please let us know if it's a bug or idea.");
        }

        $mailer->sendTo(
            $sendTo,
            $subject,
            'emails.feedback.feedback',
            ['fullName' => $fullName, 'email' => $currentUser->email, 'content' => $request->description]
        );

        return response()->json([], Response::HTTP_CREATED);
    }
}
