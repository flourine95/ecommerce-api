<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    /**
     * Success response
     */
    public static function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];

        // Handle pagination
        if ($data instanceof LengthAwarePaginator) {
            $response['data'] = [
                'items' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ]
            ];
        }

        return response()->json($response, $code);
    }

    /**
     * Error response
     */
    public static function error(string $message = 'Error', int $code = 400, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Validation error response
     */
    public static function validationError($errors, string $message = 'Validation failed'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => 422,
            'message' => $message,
            'data' => [
                'errors' => $errors
            ]
        ], 422);
    }

    /**
     * Not found response
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => 404,
            'message' => $message,
            'data' => null
        ], 404);
    }

    /**
     * Unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => 401,
            'message' => $message,
            'data' => null
        ], 401);
    }

    /**
     * Forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => 403,
            'message' => $message,
            'data' => null
        ], 403);
    }

    /**
     * Server error response
     */
    public static function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => 500,
            'message' => $message,
            'data' => null
        ], 500);
    }
}
