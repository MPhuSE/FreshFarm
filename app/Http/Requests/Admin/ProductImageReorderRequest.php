<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate cho PATCH /api/v1/admin/products/{id}/images/reorder
 * Body dạng: { "images": [{"id":81,"sort_order":1,"is_primary":true}, ...] }
 */
class ProductImageReorderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'min:1'],
            'images.*.id' => ['required', 'integer', 'distinct'],
            'images.*.sort_order' => ['required', 'integer', 'min:0'],
            'images.*.is_primary' => ['required', 'boolean'],
        ];
    }
}