<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/\S+/'],

            'password'   => 'nullable|min:6|confirmed',
            'phone'      => 'nullable|string|max:255',
            'address'    => 'nullable|string|max:255',
            'avatar'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'id_country' => 'nullable|exists:countries,id',
        ];
    }
}
