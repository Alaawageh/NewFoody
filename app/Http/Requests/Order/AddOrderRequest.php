<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_id' => ['required' , Rule::exists('tables' , 'id')],
            'branch_id' => ['required' , Rule::exists('branches' , 'id')],
            'is_paid' => 'in:0,1',
            'is_update' => 'in:0,1',
            // 'total_price' => 'required|numeric',
            'time' =>'nullable|date',
            'time_end' =>'nullable|date',
            'time_Waiter' =>'nullable|date',
            'products' => 'array',
            'products.*.id' => ['required' , Rule::exists('products' , 'id')],
            'products.*.qty' => 'nullable|numeric',
            'products.*.note' => 'nullable|string',
            'products.*.extraIng' => 'nullable|array',
            // 'products.*.productSubtotal' =>'numeric'
            // 'products.*.extraIng.*.id' => [Rule::exists('extra_ingredients' , 'id')],

        ];
    }
}
