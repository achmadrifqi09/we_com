<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserSignupRequest extends FormRequest
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
            'name' => ['required', 'min:3', 'max:120', 'regex:/^[a-zA-Z ]+$/'],
            'username' => ['required', 'min:3', 'max:120', 'unique:users,username'],
            'email' => ['required', 'min:6', 'max:255', 'unique:users,email', 'email'],
            'phone' => ['required', 'min:8', 'max:20', 'regex:/^[0-9]+$/'],
            'password' => ['required', 'min:6', 'max:100']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'errors' => $validator->getMessageBag()
        ], 400));
    }
}
