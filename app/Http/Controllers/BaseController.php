<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Success response
     */
    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $code);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message = 'Error', int $code = 400, $data = null): JsonResponse
    {
        return ApiResponse::error($message, $code, $data);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse($errors, string $message = 'Validation failed'): JsonResponse
    {
        return ApiResponse::validationError($errors, $message);
    }

    /**
     * Not found response
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return ApiResponse::notFound($message);
    }

    /**
     * Unauthorized response
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return ApiResponse::unauthorized($message);
    }

    /**
     * Forbidden response
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return ApiResponse::forbidden($message);
    }

    /**
     * Server error response
     */
    protected function serverErrorResponse(string $message = 'Internal server error'): JsonResponse
    {
        return ApiResponse::serverError($message);
    }
}
