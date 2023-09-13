<?php

namespace App\Http\Requests\Order;

use App\Types\OrderStatus;
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
            'status' => OrderStatus::BEFOR_PREPARING,
            // 'branch_id' => Rule::exists('branches' , 'id'),
            'products.*.id' => ['required' , Rule::exists('products' , 'id')],

        ];
    }
}
