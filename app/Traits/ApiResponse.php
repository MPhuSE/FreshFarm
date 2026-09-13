<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Trả về response thành công.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @param array|null $meta
     * @param array|null $links
     * @return JsonResponse
     */
    protected function respondSuccess(
        $data = null,
        string $message = 'Thành công',
        int $code = 200,
        ?array $meta = null,
        ?array $links = null
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'errors' => null,
        ];

        if ($links !== null) {
            $response['links'] = $links;
        }

        return response()->json($response, $code);
    }

    /**
     * Trả về response lỗi.
     *
     * @param string $message
     * @param string|null $errorCode
     * @param mixed $errors
     * @param int $code
     * @param string|null $traceId
     * @return JsonResponse
     */
    protected function respondError(
        string $message = 'Có lỗi xảy ra',
        ?string $errorCode = null,
        $errors = null,
        int $code = 400,
        ?string $traceId = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'error_code' => $errorCode,
            'errors' => $errors,
            'trace_id' => $traceId,
        ], $code);
    }
}
