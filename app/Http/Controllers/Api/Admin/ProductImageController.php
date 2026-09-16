<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductImageReorderRequest;
use App\Http\Requests\Admin\ProductImageStoreRequest;
use App\Http\Resources\ProductImageResource;
use App\Services\ProductImageService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductImageController extends Controller
{
    public function __construct(private readonly ProductImageService $productImageService) {}

    /** POST /api/v1/admin/products/{id}/images */
    #[OA\Post(
        path: '/api/v1/admin/products/{id}/images',
        summary: 'Upload ảnh sản phẩm',
        security: [['bearerAuth' => []]],
        tags: ['Admin Products']
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['images[]'],
                properties: [
                    new OA\Property(
                        property: 'images[]',
                        type: 'array',
                        items: new OA\Items(type: 'string', format: 'binary')
                    ),
                ]
            )
        )
    )]
    #[OA\Response(response: 201, description: 'Admin - upload nhiều ảnh thành công.')]
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
    #[OA\Patch(
        path: '/api/v1/admin/products/{id}/images/reorder',
        summary: 'Sắp xếp lại thứ tự ảnh',
        security: [['bearerAuth' => []]],
        tags: ['Admin Products']
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'images',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'sort_order', type: 'integer'),
                        ]
                    )
                ),
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Admin - sắp thứ tự ảnh thành công.')]
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
