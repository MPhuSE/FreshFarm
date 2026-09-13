<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponseTrait
{
    /** 
     * Response thành công dạng object/array đơn (không phân trang).
     */
    protected function success(
        mixed $data = null,
        string $message = 'Thao tác thành công.',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => null,
            'errors' => null,
        ], $status);
    }

    /**
     * Response thành công HTTP 201 (tạo mới resource).
     */
    protected function created(mixed $data = null, string $message = 'Tạo mới thành công.'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Response thành công không có payload (vd: DELETE, logout).
     */
    protected function noContent(string $message = 'Thao tác thành công.'): JsonResponse
    {
        return $this->success(null, $message, 200);
    }

    /**
     * Response thành công dạng danh sách CÓ phân trang (kèm meta + links).
     * $resource: đã bọc qua API Resource Collection.
     */
    protected function successPaginated(
        ResourceCollection|JsonResource|array $resource,
        LengthAwarePaginator $paginator,
        string $message = 'Lấy danh sách thành công.'
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'errors' => null,
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ], 200);
    }

    /** 
     * Response lỗi nghiệp vụ thủ công (ít dùng trực tiếp — ưu tiên throw ApiException
     * trong Service để Handler tự bắt và format).
     */
    protected function error(
        string $message,
        string $errorCode,
        int $status = 400,
        ?array $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'error_code' => $errorCode,
            'errors' => $errors,
            'trace_id' => request()->attributes->get('trace_id'),
        ], $status);
    }
}