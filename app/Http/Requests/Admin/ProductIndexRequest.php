<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validate query string cho GET /api/v1/admin/products
 * Khác với bản Public: có thêm filter theo status (draft/active/inactive)
 * vì Admin cần xem cả sản phẩm chưa publish.
 */
class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // đã chặn quyền ở middleware role:staff,admin
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:190'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', Rule::in(['draft', 'active', 'inactive'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}