<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                Rule::in([$this->user()->email]),
            ],

            'password' => 'nullable|min:6',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'id_country' => 'nullable|integer',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
        ];
    }
    public function messages(): array
    {
        return [
            'email.in' => 'ko được phép đổi email',
        ];
    }

}
