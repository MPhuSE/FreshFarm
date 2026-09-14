<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'address_id' => ['required', 'integer'],
            'payment_method' => ['required', 'string', 'in:cod,bank_transfer'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ];
    }
}
