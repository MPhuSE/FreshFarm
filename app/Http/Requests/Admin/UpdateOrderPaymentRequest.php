<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'payment_status' => 'required|string|in:paid,refunded',
            'transaction_ref' => 'nullable|string|max:255',
        ];
    }
}