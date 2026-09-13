<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductService
{
    private const DETAIL_CACHE_TTL = 1800; // 30 phút

    /* =====================================================================
     |  PHẦN PUBLIC — FR-CAT-01/02 (đã hoàn thành trước đó, giữ nguyên)
     |===================================================================== */

    public function paginatePublic(array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 12), 100);

        $query = Product::query()
            ->with(['category', 'inventory', 'primaryImage'])
            ->active();

        if (! empty($filters['q'])) {
            $query->whereFullText(['name', 'short_description'], $filters['q']);
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (array_key_exists('in_stock', $filters) && filter_var($filters['in_stock'], FILTER_VALIDATE_BOOLEAN)) {
            $query->whereHas('inventory', function ($q) {
                $q->whereColumn('quantity_on_hand', '>', 'quantity_reserved');
            });
        }

        match ($filters['sort'] ?? null) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name_asc' => $query->orderBy('name'),
            default => $query->latest('created_at'),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    public function findPublicBySlug(string $slug): Product
    {
        $product = Cache::remember(
            $this->detailCacheKey($slug),
            self::DETAIL_CACHE_TTL,
            fn () => Product::query()
                ->with(['category', 'images', 'inventory'])
                ->active()
                ->where('slug', $slug)
                ->first()
        );

        if (! $product) {
            throw new ApiException('Không tìm thấy sản phẩm.', 'PRODUCT_NOT_FOUND', 404);
        }

        return $product;
    }

    public function flushDetailCache(string $slug): void
    {
        Cache::forget($this->detailCacheKey($slug));
    }

    private function detailCacheKey(string $slug): string
    {
        return "catalog:product:{$slug}";
    }

    /* =====================================================================
     |  PHẦN ADMIN — FR-ADM-01/02 (mới thêm)
     |===================================================================== */

    /**
     * GET /api/v1/admin/products
     * Khác Public: KHÔNG lọc ->active() (Admin cần thấy cả draft/inactive),
     * cho phép lọc thêm theo status.
     */
    public function paginateAdmin(array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        $query = Product::query()
            ->with(['category', 'inventory', 'primaryImage']);

        if (! empty($filters['q'])) {
            $query->where('name', 'like', '%'.$filters['q'].'%');
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('created_at')->paginate($perPage)->withQueryString();
    }

    /**
     * GET /api/v1/admin/products/{id}
     * Trả PRODUCT_NOT_FOUND thay vì để ModelNotFoundException tự bắt,
     * để đúng error_code chuẩn API Spec (thay vì NOT_FOUND generic).
     */
    public function findAdminById(int $id): Product
    {
        $product = Product::with(['category', 'images', 'inventory'])->find($id);

        if (! $product) {
            throw new ApiException('Không tìm thấy sản phẩm.', 'PRODUCT_NOT_FOUND', 404);
        }

        return $product;
    }

    /**
     * POST /api/v1/admin/products
     * Bọc DB::transaction() vì phải ghi đồng thời 2 bảng: products + inventory.
     * Nếu 1 trong 2 lỗi, rollback toàn bộ — không để sản phẩm tồn tại mà
     * thiếu dòng tồn kho (sẽ gãy công thức available_quantity).
     */
    public function createAdmin(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // 1. Check SKU trùng — kiểm tra cả bản ghi đã soft-delete vì
            // cột sku có UNIQUE constraint ở tầng DB, không phân biệt deleted_at.
            if (Product::withTrashed()->where('sku', $data['sku'])->exists()) {
                throw new ApiException('Mã SKU đã tồn tại.', 'SKU_EXISTS', 409);
            }

            // 2. Tự sinh slug từ tên, KHÔNG nhận slug từ client (BR-01).
            $slug = Str::slug($data['name']);
            if (Product::withTrashed()->where('slug', $slug)->exists()) {
                throw new ApiException(
                    'Tên sản phẩm tạo ra slug trùng với sản phẩm đã có, vui lòng đổi tên khác.',
                    'SLUG_EXISTS',
                    409
                );
            }

            // 3. API Spec đặt tên field "content_html" trong request mẫu,
            // nhưng cột DB thật là "description_html" — ưu tiên description_html
            // nếu có, fallback sang content_html để tương thích cả 2 cách gọi.
            $descriptionHtml = $data['description_html'] ?? $data['content_html'] ?? null;

            $product = Product::create([
                'category_id' => $data['category_id'],
                'sku' => $data['sku'],
                'name' => $data['name'],
                'slug' => $slug,
                'unit' => $data['unit'],
                'origin' => $data['origin'] ?? null,
                'price' => $data['price'],
                'compare_at_price' => $data['compare_at_price'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'description_html' => $this->sanitizeHtml($descriptionHtml),
                'status' => $data['status'] ?? 'draft',
                'featured' => $data['featured'] ?? false,
            ]);

            // 4. Khởi tạo tồn kho ngay khi tạo sản phẩm — bắt buộc, vì
            // available_quantity (ProductResource) cần có dòng inventory
            // mới tính đúng, tránh trường hợp sản phẩm "mồ côi" tồn kho.
            Inventory::create([
                'product_id' => $product->id,
                'quantity_on_hand' => $data['quantity_on_hand'] ?? 0,
                'quantity_reserved' => 0,
                'reorder_level' => 0,
            ]);

            return $product->load(['category', 'images', 'inventory']);
        });
    }

    /**
     * PATCH /api/v1/admin/products/{id}
     */
    public function updateAdmin(int $id, array $data): Product
    {
        return DB::transaction(function () use ($id, $data) {
            $product = Product::with(['category', 'images', 'inventory'])->find($id);

            if (! $product) {
                throw new ApiException('Không tìm thấy sản phẩm.', 'PRODUCT_NOT_FOUND', 404);
            }

            // Check SKU trùng nếu client đổi SKU (loại trừ chính nó).
            if (isset($data['sku']) && $data['sku'] !== $product->sku) {
                $exists = Product::withTrashed()
                    ->where('sku', $data['sku'])
                    ->where('id', '!=', $product->id)
                    ->exists();

                if ($exists) {
                    throw new ApiException('Mã SKU đã tồn tại.', 'SKU_EXISTS', 409);
                }
            }

            // Cố ý KHÔNG tự sinh lại slug khi đổi "name" lúc update, để tránh
            // gãy link cũ (SEO/permalink) đã chia sẻ ra ngoài. Nếu cần đổi slug
            // thật sự, nên làm endpoint riêng có cảnh báo rõ ràng cho Admin.
            $oldSlug = $product->slug;

            if (isset($data['description_html']) || isset($data['content_html'])) {
                $data['description_html'] = $this->sanitizeHtml(
                    $data['description_html'] ?? $data['content_html'] ?? null
                );
            }
            unset($data['content_html']);

            $product->fill($data);

            // Validate chéo price/compare_at_price SAU khi merge dữ liệu mới
            // với dữ liệu cũ trong DB (không thể làm ở FormRequest vì đây là
            // partial update, có thể chỉ 1 trong 2 field được gửi lên).
            if (
                $product->compare_at_price !== null
                && (float) $product->compare_at_price < (float) $product->price
            ) {
                throw new ApiException(
                    'Giá so sánh (compare_at_price) phải lớn hơn hoặc bằng giá bán.',
                    'VALIDATION_ERROR',
                    422,
                    ['compare_at_price' => ['Giá so sánh phải >= giá bán.']]
                );
            }

            $product->save();

            // Flush cache chi tiết sản phẩm — bắt buộc, nếu không Admin sửa
            // xong mà Client vẫn thấy dữ liệu cũ tới khi cache tự hết hạn (30 phút).
            $this->flushDetailCache($oldSlug);

            return $product->fresh(['category', 'images', 'inventory']);
        });
    }

    /**
     * DELETE /api/v1/admin/products/{id} — ẩn/xóa mềm (soft delete).
     * Chặn xóa nếu sản phẩm đang nằm trong đơn hàng "active" (BR-09).
     */
    public function deleteAdmin(int $id): void
    {
        DB::transaction(function () use ($id) {
            $product = Product::find($id);

            if (! $product) {
                throw new ApiException('Không tìm thấy sản phẩm.', 'PRODUCT_NOT_FOUND', 404);
            }

            // Bảng order_items thuộc phạm vi TV3, CHƯA có migration tại thời
            // điểm này. Dùng Schema::hasTable() để guard, tránh crash nếu
            // TV3 chưa merge migration của họ. Khi order_items đã tồn tại,
            // điều kiện thật sẽ tự động được áp dụng mà không cần sửa code.
            if (Schema::hasTable('order_items') && Schema::hasTable('orders')) {
                $inActiveOrder = DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('order_items.product_id', $product->id)
                    ->whereNotIn('orders.status', ['cancelled', 'returned'])
                    ->exists();

                if ($inActiveOrder) {
                    throw new ApiException(
                        'Không thể xóa sản phẩm đang có trong đơn hàng.',
                        'PRODUCT_IN_ACTIVE_ORDER',
                        409
                    );
                }
            }

            $this->flushDetailCache($product->slug);

            $product->delete(); // soft delete — deleted_at được set, không xóa vật lý
        });
    }

    /**
     * Sanitize HTML cơ bản chống XSS cho description_html/content_html.
     * MVP: dùng strip_tags whitelist thẻ an toàn. Có thể nâng cấp lên
     * HTMLPurifier sau nếu cần lọc kỹ hơn (style/class nguy hiểm trong thẻ).
     */
    private function sanitizeHtml(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $allowedTags = '<p><br><strong><em><ul><ol><li><h1><h2><h3><h4><a><img><blockquote><span>';

        return strip_tags($html, $allowedTags);
    }
}