<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Nghiệp vụ ảnh sản phẩm: gán sort_order, xác định is_primary, validate
 * thứ tự khi reorder. KHÔNG tự viết code lưu/xóa file ở đây — mọi thao tác
 * với ổ đĩa đều gọi qua UploadService để tránh trùng lặp logic.
 */
class ProductImageService
{
    public function __construct(
        private readonly UploadService $uploadService,
        private readonly ProductService $productService,
    ) {
    }

    /**
     * POST /api/v1/admin/products/{id}/images
     *
     * @param  UploadedFile[]  $files
     * @param  string[]  $altTexts
     */
    public function uploadImages(int $productId, array $files, array $altTexts = []): Collection
    {
        $product = $this->productService->findAdminById($productId);

        $storedPaths = [];

        try {
            $hasExistingImage = $product->images()->exists();
            $nextSortOrder = (int) $product->images()->max('sort_order');

            $rows = [];

            foreach ($files as $index => $file) {
                $path = $this->uploadService->storeImage($file, "products/{$product->id}");
                $storedPaths[] = $path;

                $nextSortOrder++;

                $rows[] = [
                    'product_id' => $product->id,
                    'file_path' => $path,
                    'alt_text' => $altTexts[$index] ?? null,
                    'sort_order' => $nextSortOrder,
                    'is_primary' => (! $hasExistingImage && $index === 0),
                    'created_at' => now(),
                ];
            }

            return DB::transaction(function () use ($rows, $product) {
                ProductImage::insert($rows);

                $this->productService->flushDetailCache($product->slug);

                return $product->images()->orderBy('sort_order')->get();
            });
        } catch (\Throwable $e) {
            if (! empty($storedPaths)) {
                $this->uploadService->delete($storedPaths);
            }

            throw $e;
        }
    }

    /**
     * PATCH /api/v1/admin/products/{id}/images/reorder
     *
     * @param  array<int, array{id:int, sort_order:int, is_primary:bool}>  $imagesData
     */
    public function reorderImages(int $productId, array $imagesData): Collection
    {
        $product = $this->productService->findAdminById($productId);

        return DB::transaction(function () use ($product, $imagesData) {
            $imageIds = collect($imagesData)->pluck('id');

            $existingImages = ProductImage::where('product_id', $product->id)
                ->whereIn('id', $imageIds)
                ->get()
                ->keyBy('id');

            if ($existingImages->count() !== $imageIds->count()) {
                throw new ApiException(
                    'Danh sách ảnh không hợp lệ: có ảnh không thuộc sản phẩm này.',
                    'INVALID_IMAGE_ORDER',
                    422
                );
            }

            $primaryCount = collect($imagesData)->where('is_primary', true)->count();

            if ($primaryCount !== 1) {
                throw new ApiException(
                    'Phải chọn đúng 1 ảnh làm ảnh đại diện (is_primary).',
                    'INVALID_IMAGE_ORDER',
                    422
                );
            }

            foreach ($imagesData as $item) {
                $existingImages[$item['id']]->update([
                    'sort_order' => $item['sort_order'],
                    'is_primary' => $item['is_primary'],
                ]);
            }

            $this->productService->flushDetailCache($product->slug);

            return $product->images()->orderBy('sort_order')->get()->fresh();
        });
    }
}