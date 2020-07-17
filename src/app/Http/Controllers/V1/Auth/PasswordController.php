<?php

namespace App\Http\Controllers\V1\Auth;

use App\Contracts\User\UserInterface;
use App\Events\PasswordChanged;
use App\Events\PasswordReset;
use App\Exceptions\ValidationFailedException;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\User\UserChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class PasswordController extends Controller
{
    /**
     * User Contract implementation
     *
     * @var UserContract
     */
    private $user;

    /**
     * @param UserContract $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Change password of currently logged-in user
     *
     * @param UserChangePasswordRequest $request
     * @return Response
     * @throws ValidationFailedException
     */
    public function changePassword(UserChangePasswordRequest $request)
    {
        $currentPassword = $request->get('current_password');
        $newPassword = $request->get('new_password');
        $user = Auth::user();

        if (Auth::attempt(['email' => $user->email, 'password' => $currentPassword])) {
            // Authentication passed...
            $this->user->changePassword($user->id, $newPassword);
            Event::fire(new PasswordChanged($user, $newPassword));
            return response(null, Response::HTTP_NO_CONTENT);
        } else {
            throw new ValidationFailedException('Oops. You filled in the wrong password.');
        }
    }

    /**
     * Reset the user's password to a random password
     *
     * @param Request $request
     * @return Response
     */
    public function resetPassword(Request $request)
    {
        $email = $request->get('email');
        $user = $this->user->findByEmail($email);
        $newPassword = $this->user->resetPassword($user);

        Event::fire(new PasswordReset($user, $newPassword));
        return response(null, Response::HTTP_CREATED);
    }

}
