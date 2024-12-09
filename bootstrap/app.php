<?php

use App\Enums\ResponseStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {

            $exceptionMap = [
                AccessDeniedHttpException::class => ['This action is unauthorized.', Response::HTTP_FORBIDDEN],
                QueryException::class => ['Un error occured when tryning access the database', Response::HTTP_INTERNAL_SERVER_ERROR],
                MethodNotAllowedHttpException::class => ['Invalid HTTP verb.', Response::HTTP_METHOD_NOT_ALLOWED],
                ModelNotFoundException::class => ['Resource not found.', Response::HTTP_NOT_FOUND],
                AuthorizationException::class => ['You do not have permission to perform this action.', Response::HTTP_FORBIDDEN],
                NotFoundHttpException::class => ['Route not found.', Response::HTTP_NOT_FOUND],
                BadRequestHttpException::class => ['Bad request.', Response::HTTP_BAD_REQUEST],
                ConflictHttpException::class => ['Resource conflict.', Response::HTTP_CONFLICT],
                ServiceUnavailableHttpException::class => ['Service unavailable. Please try again later.', Response::HTTP_SERVICE_UNAVAILABLE],
            ];

            foreach ($exceptionMap as $exceptionClass => [$message, $statusCode]) {
                if ($e instanceof $exceptionClass) {
                    return response()->json([
                        'status' => ResponseStatus::ERROR->value,
                        'message' => $message,
                        'code' => $statusCode
                    ], $statusCode);
                }
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'status' => ResponseStatus::ERROR->value,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });

    })->create();
