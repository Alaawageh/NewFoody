<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditOrderRequest extends FormRequest
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
            'table_id' => ['required' , Rule::exists('tables' , 'id')],
            'branch_id' => ['required' , Rule::exists('branches' , 'id')],
            'products' => 'array',
            'products.*.id' => ['required' , Rule::exists('products' , 'id')],
            'products.*.qty' => 'nullable|numeric',
            'products.*.note' => 'nullable|string',
            'products.*.extraIng' => 'nullable|array',
        ];
    }
}
