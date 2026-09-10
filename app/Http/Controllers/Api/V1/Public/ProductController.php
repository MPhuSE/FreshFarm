<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ProductIndexRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(ProductIndexRequest $request): JsonResponse
    {
        $products = $this->productService->paginatePublic($request->validated());

        return $this->successPaginated(
            ProductResource::collection($products),
            $products,
            'Lấy danh sách và tìm kiếm sản phẩm thành công.'
        );
    }

    public function show(string $slug): JsonResponse
    {
        $product = $this->productService->findPublicBySlug($slug);

        return $this->success(
            new ProductDetailResource($product),
            'Lấy chi tiết sản phẩm thành công.'
        );
    }
}