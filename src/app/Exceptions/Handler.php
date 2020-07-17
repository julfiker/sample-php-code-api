<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        return $this->APIExceptionResponses($request, $e);
    }

    private function APIExceptionResponses($request, Exception $e)
    {
        // IF INSTANCE OF HTTPEXCEPTION
        if ($e instanceof HttpException)
        {
            $response = response()->json([
                'rootMessage' => $e->getMessage()
            ], $e->getStatusCode(), $e->getHeaders() + ['Content-Type' => 'application/json']);

        // IF INSTANCE OF MODELNOTFOUNDEXCEPTION
        } elseif ($e instanceof ModelNotFoundException) {

            $response =  response()->json([
                'rootMessage' => 'Not found...'
            ],  Response::HTTP_NOT_FOUND);

        // IF INSTANCE QUERYEXCEPTION
        } elseif ($e instanceof QueryException) {
            $response =  response()->json([
                'rootMessage' => 'Database error...' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        elseif ($e instanceof ValidationFailedException) {
            $response =  response()->json([
                'rootMessage' => 'Validation error',
                'messages' => [$e->getMessage()]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            print_r($e->getMessage());
            $response =  response()->json([
                'rootMessage' => $e->getMessage(),
            ],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // adding the CORS headers
        // TODO: Use the AddHeaders middleware to do this job
        return $response
            ->header('Access-Control-Allow-Origin' , '*')
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
            ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With')
            ->header('Access-Control-Expose-Headers', 'X-Auth-Token, Authorization')
            ->header('Access-Control-Max-Age', '3600');

    }
}
