<?php

namespace App\Traits;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse($data, string $message = 'Thành công!', int $statusCode = 200 , $meta = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'errors' => null,
        ], $statusCode);
    }

    protected function errorResponse( string $message, string $errorCode, int $statusCode = 400 , $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors_code' => $errorCode,
            'errors' => $errors,
            'trace_id' => 'req_' . uniqid(),
        ], $statusCode);
    }
}