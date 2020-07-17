<?php

namespace App\Contracts\Auth;

use Illuminate\Http\Request;

interface AuthJwtInterface
{

    public function validate(Request $request);

    public function extractTokenFromHeader(Request $request);

    public function encode($userID);

    public function decode($token);

    public function currentUser($token);

}