<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductIndexRequest;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * Controller MỎNG theo đúng nguyên tắc Clean Architecture: chỉ nhận Request,
 * gọi Service, trả Response qua ApiResponseTrait. Toàn bộ transaction,
 * kiểm tra SKU/SLUG trùng, sanitize HTML nằm ở ProductService.
 */
class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    /** GET /api/v1/admin/products */
    #[OA\Get(
        path: '/api/v1/admin/products',
        summary: 'Danh sách sản phẩm (Admin)',
        security: [['bearerAuth' => []]],
        tags: ['Admin Products']
    )]
    #[OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Admin - danh sách sản phẩm thành công.')]
    public function index(ProductIndexRequest $request): JsonResponse
    {
        $products = $this->productService->paginateAdmin($request->validated());

        return $this->successPaginated(
            ProductResource::collection($products),
            $products,
            'Admin - danh sách sản phẩm thành công.'
        );
    }

    /** POST /api/v1/admin/products */
    #[OA\Post(
        path: '/api/v1/admin/products',
        summary: 'Thêm mới sản phẩm',
        security: [['bearerAuth' => []]],
        tags: ['Admin Products']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'price'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Product Name'),
                new OA\Property(property: 'price', type: 'number', example: 100000),
                new OA\Property(property: 'category_id', type: 'integer', example: 1),
                new OA\Property(property: 'description', type: 'string', example: 'Mô tả sản phẩm'),
                new OA\Property(property: 'stock', type: 'integer', example: 10)
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Admin - tạo sản phẩm thành công.')]
    public function store(ProductStoreRequest $request): JsonResponse
    {
        $product = $this->productService->createAdmin($request->validated());

        return $this->created(
            new ProductDetailResource($product),
            'Admin - tạo sản phẩm thành công.'
        );
    }

    /** GET /api/v1/admin/products/{id} */
    #[OA\Get(
        path: '/api/v1/admin/products/{id}',
        summary: 'Lấy chi tiết sản phẩm',
        security: [['bearerAuth' => []]],
        tags: ['Admin Products']
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Admin - chi tiết sản phẩm thành công.')]
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findAdminById($id);

        return $this->success(
            new ProductDetailResource($product),
            'Admin - chi tiết sản phẩm thành công.'
        );
    }

    /** PATCH /api/v1/admin/products/{id} */
    #[OA\Patch(
        path: '/api/v1/admin/products/{id}',
        summary: 'Cập nhật sản phẩm',
        security: [['bearerAuth' => []]],
        tags: ['Admin Products']
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Product Name Updated'),
                new OA\Property(property: 'price', type: 'number', example: 120000),
                new OA\Property(property: 'category_id', type: 'integer', example: 1),
                new OA\Property(property: 'description', type: 'string', example: 'Mô tả sản phẩm đã cập nhật'),
                new OA\Property(property: 'stock', type: 'integer', example: 15)
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Admin - cập nhật sản phẩm thành công.')]
    public function update(ProductUpdateRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->updateAdmin($id, $request->validated());

        return $this->success(
            new ProductDetailResource($product),
            'Admin - cập nhật sản phẩm thành công.'
        );
    }

    /** DELETE /api/v1/admin/products/{id} */
    #[OA\Delete(
        path: '/api/v1/admin/products/{id}',
        summary: 'Xóa mềm sản phẩm',
        security: [['bearerAuth' => []]],
        tags: ['Admin Products']
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 204, description: 'Admin - ẩn/xóa mềm sản phẩm thành công.')]
    public function destroy(int $id): JsonResponse
    {
        $this->productService->deleteAdmin($id);

        return $this->noContent('Admin - ẩn/xóa mềm sản phẩm thành công.');
    }
}
