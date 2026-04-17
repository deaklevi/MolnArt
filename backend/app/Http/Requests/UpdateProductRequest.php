<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name'   => 'nullable|string|max:255',
            'stock'  => 'nullable|numeric|min:0',
            'unit'   => 'nullable|string|max:50',
            'type'   => 'nullable|integer|between:1,20',
            'amount' => 'nullable|numeric',
        ];
    }
}
