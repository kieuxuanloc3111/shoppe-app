<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'category_id' => 'required',
            'brand_id'    => 'required',
            'sale'        => 'required|in:0,1',
            'sale_price'  => 'nullable|numeric',
            'company'     => 'required|string',
            'detail'      => 'required|string',

            'images'      => 'required|array|max:3',
            'images.*'    => 'image|max:1024', // < 1MB
        ];
    }
}
