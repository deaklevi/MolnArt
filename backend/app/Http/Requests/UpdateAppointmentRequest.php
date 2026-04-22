<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appointment_from' => ['sometimes', 'date'],
            'appointment_to' => ['sometimes', 'date', 'after:appointment_from'],
            'service' => ['sometimes', 'string', 'max:60'],
            'customer_id' => ['sometimes', 'exists:customers,id'],
            'name' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', 'max:100'],
            'phone_number' => ['sometimes', 'string', 'max:20'],
            'used_products' => ['sometimes', 'array'],
            'used_products.*.product_id' => ['required_with:used_products', 'exists:products,id'],
            'used_products.*.quantity' => ['required_with:used_products', 'numeric', 'min:0'],
        ];
    }
}
