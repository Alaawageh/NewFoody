<?php

namespace App\Http\Requests\ExtraIng;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditExtraIngRequest extends FormRequest
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
            'ingredient_id' => [Rule::exists('ingredients' , 'id')],
            'price_per_kilo' => 'numeric|required',
            'branch_id' => [Rule::exists('branches' , 'id')]
        ];
    }
}
