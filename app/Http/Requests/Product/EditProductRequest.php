<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'max:255|string',
            'name_ar' => 'nullable|string',
            'description' => 'string|max:2500',
            'description_ar' => 'nullable|string',
            'price' => 'numeric|min:0',
            'position' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,jpg,png',
            'estimated_time' => 'nullable|date_format:H:i:s',
            'status' => 'in:0,1',
            'category_id' => [Rule::exists('categories' , 'id')],
            'branch_id' => [Rule::exists('branches' , 'id')],
            'ingredients.*.id' => [Rule::exists('ingredients' , 'id')],
            'ingredients.*.quantity' => 'numeric',
            'ingredients.*.is_remove' => 'in:0,1',
            'extra_ingredients.*.id' => ['nullable' , Rule::exists('extra_ingredients' , 'id')],
            'extra_ingredients.*.quantity' => 'nullable|numeric',
        ];
    }
}
