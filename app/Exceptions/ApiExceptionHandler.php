<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Exceptions\NoCurrentTenant;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiExceptionHandler
{
    public static function handle(Exception $e, Request $request)
    {
        if (! $request->is('api/*')) {
            return null; // Let Laravel handle non-API requests normally
        }

        return match (true) {
            $e instanceof AuthenticationException => self::handleAuthenticationException($e, $request),
            $e instanceof NotFoundHttpException => self::handleNoResourceNotfoundException($e, $request),
            $e instanceof AuthorizationException => ApiResponse::forbidden(message: 'Access denied.'),
            $e instanceof ThrottleRequestsException => self::handleThrottleException($e, $request),
            $e instanceof NoCurrentTenant => self::handleNoCurrentTenantException($e, $request),
            default => self::handleGenericException($e),
        };
    }

    private static function handleAuthenticationException(AuthenticationException $e, Request $request): JsonResponse
    {
        $message = $request->hasHeader('Authorization')
            ? 'Invalid or expired token.'
            : 'No authentication token provided.';

        return ApiResponse::unauthorized(message: $message);
    }

    private static function handleNoCurrentTenantException(NoCurrentTenant $e, Request $request): JsonResponse
    {
        return ApiResponse::notFound(message: __('app.tenant_missing'));
    }

    private static function handleNoResourceNotfoundException(NotFoundHttpException $e, Request $request): JsonResponse
    {
        return ApiResponse::notFound(message: $e->getMessage());
    }

    private static function handleThrottleException(ThrottleRequestsException $e, Request $request): JsonResponse
    {
        return ApiResponse::tooManyRequests(
            message: 'Too many requests. Please try again later.',
            retryAfter: $e->getHeaders()['Retry-After'] ?? null
        );
    }

    private static function handleGenericException($e): JsonResponse
    {
        dd($e);

        return ApiResponse::serverError(message: 'there is an error please try again later or contact with support for fast response');

    }
}
