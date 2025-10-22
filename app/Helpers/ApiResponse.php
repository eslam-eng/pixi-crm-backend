<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    public static function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error(string $message = 'Error', $errors = [], int $code = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $code);
    }

    public static function serverError(string $message = 'Server Error', $errors = []): JsonResponse
    {
        return self::error($message, $errors, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function tooManyRequests($message = 'Too many requests', $data = null, $retryAfter = null)
    {
        $response = response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'error_code' => 'TOO_MANY_REQUESTS',
        ], 429);

        // Add Retry-After header if provided
        if ($retryAfter) {
            $response->header('Retry-After', $retryAfter);
        }

        return $response;
    }

    public static function badRequest(string $message = 'Bad Request', $errors = []): JsonResponse
    {
        return self::error($message, $errors, Response::HTTP_BAD_REQUEST);
    }

    public static function validationErrors(string $message = 'validation error', $errors = []): JsonResponse
    {
        return self::error($message, $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function notFound(string $message = 'Resource Not Found', $errors = []): JsonResponse
    {
        return self::error($message, $errors, Response::HTTP_NOT_FOUND);
    }

    public static function unauthorized(string $message = 'Unauthorized', $errors = []): JsonResponse
    {
        return self::error($message, $errors, Response::HTTP_UNAUTHORIZED);
    }

    public static function forbidden(string $message = 'Forbidden', $errors = []): JsonResponse
    {
        return self::error($message, $errors, Response::HTTP_FORBIDDEN);
    }
}
