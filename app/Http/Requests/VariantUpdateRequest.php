<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VariantUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'variants.*.id' => ['required'],
            'variants.*.name' => ['required', 'min:1', 'max:12'],
            'variants.*.price' => ['required', 'numeric'],
            'variants.*.quantity' => ['required', 'numeric'],
        ];
    }
}
