<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool { return true; } // Đã check role ở Middleware

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:confirmed,shipping,delivered,cancelled,returned',
            'note' => 'nullable|string|max:500',
        ];
    }
}