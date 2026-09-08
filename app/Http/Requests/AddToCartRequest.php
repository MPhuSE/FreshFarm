<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddToCartRequest extends FormRequest
{
   
    public function authorize(): bool
    {
        return true ; //cho request đi qua để validate
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer',
            'quantity' => 'required|numeric|min:1', // >0 
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'data' => null,
            'errors_code' => 'VALIDATION_ERROR',
            'errors' => $validator->errors(),
            'trace_id' => 'req_' . uniqid(),
        ], 422));
    }
}
