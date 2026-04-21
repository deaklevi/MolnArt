<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'appointment_from' => ['required', 'date'],
            'appointment_to' => ['required', 'date', 'after:appointment_from'],
            'service' => ['required', 'string', 'max:100'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'name' => ['sometimes', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'phone_number' => ['sometimes', 'string', 'max:20'],
            'used_products' => ['sometimes', 'array'],
            'used_products.*.product_id' => ['required_with:used_products', 'exists:products,id'],
            'used_products.*.quantity' => ['required_with:used_products', 'numeric', 'min:0'],
        ];
    }
}
