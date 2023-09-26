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
            'category_id' => ['required' , Rule::exists('categories' , 'id')],
            'branch_id' => ['required' , Rule::exists('branches' , 'id')],
            'ingredients.*.id' => ['required' , Rule::exists('ingredients' , 'id')],
            'ingredients.*.quantity' => 'required|numeric',
            'ingredients.*.is_remove' => 'in:0,1',
            'extra_ingredients.*.id' => ['nullable' , Rule::exists('extra_ingredients' , 'id')],
            'extra_ingredients.*.quantity' => 'nullable|numeric',
        ];
    }
}
