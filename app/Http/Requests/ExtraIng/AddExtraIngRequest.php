<?php

namespace App\Http\Requests\ExtraIng;

use Illuminate\Foundation\Http\FormRequest;

class AddExtraIngRequest extends FormRequest
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
            'name' => 'string|required',
            'name_ar' => 'nullable',
            'quantity' => 'required|numeric',
            'price_per_peice' => 'numeric|required'
        ];
    }
}