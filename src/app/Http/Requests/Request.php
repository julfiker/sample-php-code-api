<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

abstract class Request extends FormRequest
{
    public function response(array $errors)
    {
        return response()->json([
            'rootMessage' => 'Validation error',
            'messages' => $errors
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
