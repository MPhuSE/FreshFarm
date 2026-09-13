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

    protected function success($data = null, string $message = 'Thành công', int $code = 200): JsonResponse
    {
        return $this->respondSuccess($data, $message, $code);
    }

    protected function created($data = null, string $message = 'Tạo mới thành công'): JsonResponse
    {
        return $this->respondSuccess($data, $message, 201);
    }

    protected function noContent(string $message = 'Thao tác thành công.'): JsonResponse
    {
        return $this->respondSuccess(null, $message, 200);
    }

    protected function successPaginated($resource, $paginator, string $message = 'Thành công'): JsonResponse
    {
        return $this->respondSuccess($resource, $message, 200, [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ], [
            'first' => $paginator->url(1),
            'last' => $paginator->url($paginator->lastPage()),
            'prev' => $paginator->previousPageUrl(),
            'next' => $paginator->nextPageUrl(),
        ]);
    }

    protected function error(string $message, string $errorCode, int $status = 400, ?array $errors = null): JsonResponse
    {
        return $this->respondError($message, $errorCode, $errors, $status, request()->attributes->get('trace_id'));
    }
}
