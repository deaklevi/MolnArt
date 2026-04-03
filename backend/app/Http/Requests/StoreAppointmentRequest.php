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
            'service' => ['required', 'string', 'max:25'],
            'customer_id' => ['required', 'exists:customers,id'],
            'name' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', 'max:100'],
            'phone_number' => ['sometimes', 'string', 'max:20'],
        ];
    }
}
