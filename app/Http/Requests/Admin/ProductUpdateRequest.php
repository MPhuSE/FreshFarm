<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validate body cho PATCH /api/v1/admin/products/{id}
 * Dùng "sometimes" cho mọi field vì đây là cập nhật một phần (partial update) —
 * Admin có thể chỉ gửi 1-2 field cần đổi, không bắt buộc gửi lại toàn bộ.
 *
 * Lưu ý: rule "gte:price" bị bỏ ở đây (khác với Store) vì lúc PATCH có thể
 * client chỉ gửi compare_at_price mà không gửi lại price cũ — validate chéo
 * 2 field lúc này sẽ sai. Việc so sánh price vs compare_at_price sau khi
 * merge với dữ liệu cũ trong DB được kiểm tra lại ở Service (xem ProductService).
 */
class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'sku' => ['sometimes', 'string', 'max:64'],
            'name' => ['sometimes', 'string', 'max:180'],
            'unit' => ['sometimes', 'string', 'max:30'],
            'origin' => ['nullable', 'string', 'max:150'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description_html' => ['nullable', 'string'],
            'content_html' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(['draft', 'active', 'inactive'])],
            'featured' => ['nullable', 'boolean'],
        ];
    }
}