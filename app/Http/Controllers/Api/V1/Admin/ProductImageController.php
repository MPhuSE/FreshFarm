<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductImageReorderRequest;
use App\Http\Requests\Admin\ProductImageStoreRequest;
use App\Http\Resources\ProductImageResource;
use App\Services\ProductImageService;
use Illuminate\Http\JsonResponse;

class ProductImageController extends Controller
{
    public function __construct(private readonly ProductImageService $productImageService)
    {
    }

    /** POST /api/v1/admin/products/{id}/images */
    public function store(ProductImageStoreRequest $request, int $id): JsonResponse
    {
        $images = $this->productImageService->uploadImages(
            $id,
            $request->file('images'),
            $request->input('alt_text', [])
        );

        return $this->created(
            ProductImageResource::collection($images),
            'Admin - upload nhiều ảnh thành công.'
        );
    }

    /** PATCH /api/v1/admin/products/{id}/images/reorder */
    public function reorder(ProductImageReorderRequest $request, int $id): JsonResponse
    {
        $images = $this->productImageService->reorderImages(
            $id,
            $request->validated('images')
        );

        return $this->success(
            ProductImageResource::collection($images),
            'Admin - sắp thứ tự ảnh thành công.'
        );
    }
}