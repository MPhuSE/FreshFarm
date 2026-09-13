<?php

namespace App\Http\Requests\Public;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // endpoint public, không cần đăng nhập
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:190'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'in_stock' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:price_asc,price_desc,name_asc,newest'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Theo API Spec, lỗi filter sản phẩm dùng error_code riêng: INVALID_FILTER
     * (khác với VALIDATION_ERROR mặc định) nên override lại failedValidation().
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException(
            'Bộ lọc tìm kiếm không hợp lệ.',
            'INVALID_FILTER',
            422,
            $validator->errors()->toArray()
        );
    }
}