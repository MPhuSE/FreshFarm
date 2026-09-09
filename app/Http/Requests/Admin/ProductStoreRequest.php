<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validate body cho POST /api/v1/admin/products
 * Lưu ý: KHÔNG nhận field "slug" từ client — slug luôn do Service tự sinh
 * từ "name" (đúng BR-01: giá/slug/dữ liệu chốt do server quyết định).
 */
class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:64'],
            'name' => ['required', 'string', 'max:180'],
            'unit' => ['required', 'string', 'max:30'],
            'origin' => ['nullable', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'gte:price'],
            'short_description' => ['nullable', 'string', 'max:500'],
            // Field trong API Spec đặt tên "content_html", nhưng cột DB thật
            // (theo hi.sql) là "description_html" — Service sẽ tự map lại.
            'description_html' => ['nullable', 'string'],
            'content_html' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['draft', 'active', 'inactive'])],
            'featured' => ['nullable', 'boolean'],
            // Khởi tạo tồn kho ban đầu — theo đúng ví dụ request trong API Spec
            'quantity_on_hand' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}