<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    private const CACHE_KEY = 'catalog:categories:active';
    private const CACHE_TTL = 3600; // 1 giờ — danh mục ít thay đổi

    public function getPublicCategories(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Category::query()
                ->active()
                ->orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Gọi khi Admin thêm/sửa/xóa danh mục để tránh trả dữ liệu cũ.
     */
    public function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}