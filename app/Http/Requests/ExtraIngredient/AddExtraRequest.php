<?php

namespace App\Http\Requests\ExtraIngredient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddExtraRequest extends FormRequest
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
            'quantity' => 'required|string',
            'price_per_piece' => 'required|numeric',
            'repo_id' => ['required' , Rule::exists('repos' , 'id')]
        ];
    }
}
