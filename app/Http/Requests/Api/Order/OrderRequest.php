<?php

namespace App\Http\Requests\Api\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'hold_id' => ['required', 'integer', 'min:1', 'exists:holds,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'hold_id.exists'   => 'the order with this ID does not exist',
            'hold_id.required' => 'order ID is required',
            'hold_id.integer'  => 'order ID must be an integer',
            'hold_id.min'      => 'order ID must be at least 1',
        ];
    }
}
