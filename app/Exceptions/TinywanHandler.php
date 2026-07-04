<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TinywanHandler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return Response
     *
     * @throws Exception
     */
    public function render($request, Exception $exception)
    {
        if (!$this->shouldReturnJson($request)) {
            return parent::render($request, $exception);
        }

        if ($exception instanceof ValidationException) {
            return response_json(
                $exception->validator->errors()->first(),
                0,
                [],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($exception instanceof AuthenticationException) {
            return response_json('Unauthenticated.', Response::HTTP_UNAUTHORIZED, [], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof AuthorizationException) {
            return response_json('This action is unauthorized.', Response::HTTP_FORBIDDEN, [], Response::HTTP_FORBIDDEN);
        }

        if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
            return response_json('Not Found.', Response::HTTP_NOT_FOUND, [], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response_json('Method Not Allowed.', Response::HTTP_METHOD_NOT_ALLOWED, [], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage() ?: (Response::$statusTexts[$statusCode] ?? 'Http Error.');

            return response_json($message, $statusCode, [], $statusCode);
        }

        $message = config('app.debug') ? $exception->getMessage() : 'Server Error.';

        return response_json($message, Response::HTTP_INTERNAL_SERVER_ERROR, [], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Determine whether the exception should be rendered as JSON.
     *
     * @param Request $request
     * @return bool
     */
    protected function shouldReturnJson(Request $request): bool
    {
        return $request->is('api/*') || $request->is('admin/*') || $request->expectsJson();
    }
}
