<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Category::with('parent')->orderBy('sort_order', 'asc')->orderBy('id', 'desc');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        return $this->respondSuccess($query->get(), 'Lấy danh mục thành công.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:120|unique:categories,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image_path'] = '/storage/'.$path;
        }

        $category = Category::create($validated);

        return $this->respondSuccess($category, 'Tạo danh mục thành công.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:120|unique:categories,name,'.$id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image_path'] = '/storage/'.$path;
        }

        $category->update($validated);

        return $this->respondSuccess($category, 'Cập nhật danh mục thành công.');
    }

    public function destroy(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            return $this->respondError('Không thể xóa danh mục đang có sản phẩm.', 'CATEGORY_HAS_PRODUCTS', null, 409);
        }

        if ($category->children()->exists()) {
            return $this->respondError('Không thể xóa danh mục đang có danh mục con.', 'CATEGORY_HAS_CHILDREN', null, 409);
        }

        $category->delete();

        return $this->respondSuccess(null, 'Xóa danh mục thành công.');
    }
}
