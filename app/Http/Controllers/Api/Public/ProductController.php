<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ProductIndexRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly ProductService $productService) {}

    #[OA\Get(
        path: '/api/v1/products',
        summary: 'Lấy danh sách sản phẩm',
        tags: ['Catalog']
    )]
    #[OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Lấy danh sách và tìm kiếm sản phẩm thành công.')]
    public function index(ProductIndexRequest $request): JsonResponse
    {
        $products = $this->productService->paginatePublic($request->validated());

        return $this->successPaginated(
            ProductResource::collection($products),
            $products,
            'Lấy danh sách và tìm kiếm sản phẩm thành công.'
        );
    }

    #[OA\Get(
        path: '/api/v1/products/{slug}',
        summary: 'Lấy chi tiết sản phẩm',
        tags: ['Catalog']
    )]
    #[OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Lấy chi tiết sản phẩm thành công.')]
    #[OA\Response(response: 404, description: 'Không tìm thấy sản phẩm.')]
    public function show(string $slug): JsonResponse
    {
        $product = $this->productService->findPublicBySlug($slug);

        return $this->success(
            new ProductDetailResource($product),
            'Lấy chi tiết sản phẩm thành công.'
        );
    }
}
