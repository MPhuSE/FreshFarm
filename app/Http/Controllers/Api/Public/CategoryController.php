<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService) {}

    #[OA\Get(
        path: '/api/v1/categories',
        summary: 'Lấy danh sách danh mục',
        tags: ['Catalog']
    )]
    #[OA\Response(response: 200, description: 'Lấy danh sách danh mục công khai thành công.')]
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getPublicCategories();

        return $this->success(
            CategoryResource::collection($categories),
            'Lấy danh sách danh mục công khai thành công.'
        );
    }
}
