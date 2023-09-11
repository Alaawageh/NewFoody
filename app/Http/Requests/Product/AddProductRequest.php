<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255|string',
            'name_ar' => 'nullable|string',
            'description' => 'required|string|max:2500',
            'description_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'position' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,jpg,png',
            'estimated_time' => 'nullable|date_format:H:i:s',
            'status' => 'in:0,1',
            'extraIng' => 'nullable|array',
            'extraIng.*.id' => ['required' , Rule::exists('extra_ingredients' , 'id')],
            'ingredient' => 'nullable|array',
            'ingredient.*.id' => ['required' , Rule::exists('repos' , 'id')],
            'ingredient.*.qty' => 'required|string',
            'category_id' => ['required' , Rule::exists('categories' , 'id')]
        ];
    }
}
