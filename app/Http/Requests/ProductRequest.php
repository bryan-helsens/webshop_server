<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "name" => 'required|max:1000',
            'image' => 'nullable|image',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'short_description' => 'string',
            'published' => 'required|boolean',
            'stock_status' => 'required|string',
            'slug' => 'required|max:1000',
            'status' => 'required|max:50',
            'quantity' => 'required|integer',
        ];
    }
}
