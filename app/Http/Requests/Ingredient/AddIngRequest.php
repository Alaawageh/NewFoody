<?php

namespace App\Http\Requests\Ingredient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddIngRequest extends FormRequest
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
            'name' => 'string|required',
            'name_ar' => 'nullable|string',
            'total_quantity' => 'required|integer|min:0',
            'threshold' => 'numeric',
            'branch_id' => ['required' , Rule::exists('branches' , 'id')]
        ];
    }
}
