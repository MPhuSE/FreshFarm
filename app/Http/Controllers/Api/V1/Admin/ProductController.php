<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductIndexRequest;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

/**
 * Controller MỎNG theo đúng nguyên tắc Clean Architecture: chỉ nhận Request,
 * gọi Service, trả Response qua ApiResponseTrait. Toàn bộ transaction,
 * kiểm tra SKU/SLUG trùng, sanitize HTML nằm ở ProductService.
 */
class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    /** GET /api/v1/admin/products */
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
    public function store(ProductStoreRequest $request): JsonResponse
    {
        $product = $this->productService->createAdmin($request->validated());

        return $this->created(
            new ProductDetailResource($product),
            'Admin - tạo sản phẩm thành công.'
        );
    }

    /** GET /api/v1/admin/products/{id} */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findAdminById($id);

        return $this->success(
            new ProductDetailResource($product),
            'Admin - chi tiết sản phẩm thành công.'
        );
    }

    /** PATCH /api/v1/admin/products/{id} */
    public function update(ProductUpdateRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->updateAdmin($id, $request->validated());

        return $this->success(
            new ProductDetailResource($product),
            'Admin - cập nhật sản phẩm thành công.'
        );
    }

    /** DELETE /api/v1/admin/products/{id} */
    public function destroy(int $id): JsonResponse
    {
        $this->productService->deleteAdmin($id);

        return $this->noContent('Admin - ẩn/xóa mềm sản phẩm thành công.');
    }
}