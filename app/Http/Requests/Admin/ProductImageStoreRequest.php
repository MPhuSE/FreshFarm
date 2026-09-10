<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate cho POST /api/v1/admin/products/{id}/images
 *
 * Cố ý CHỈ validate "có file hợp lệ" ở tầng này (kiểu FormRequest chuẩn của
 * Laravel), còn kiểm tra MIME thật + dung lượng cụ thể (để trả đúng
 * error_code UNSUPPORTED_MEDIA/FILE_TOO_LARGE thay vì VALIDATION_ERROR
 * chung chung) được đẩy xuống UploadService — vì đó là business rule có
 * error_code riêng theo API Spec, không phải lỗi validate thông thường.
 */
class ProductImageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['required', 'file'],
            'alt_text' => ['nullable', 'array'],
            'alt_text.*' => ['nullable', 'string', 'max:180'],
        ];
    }
}