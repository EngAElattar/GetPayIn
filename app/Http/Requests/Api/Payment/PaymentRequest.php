<?php

namespace App\Http\Requests\Api\Payment;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'idempotency_key' => 'required|string',
            'order_id'        => 'required|integer|exists:orders,id',
            'status'          => 'required|in:success,failed'
        ];
    }

    public function messages(): array
    {
        return [
            'idempotency_key.required' => 'Idempotency key is required',
            'idempotency_key.string'   => 'Idempotency key must be a string',
            'order_id.required'        => 'Order ID is required',
            'order_id.integer'         => 'Order ID must be an integer',
            'order_id.exists'          => 'Order ID does not exist',
            'status.required'          => 'Status is required',
            'status.in'                => 'Status must be "success" or "failed"',
        ];
    }
}
