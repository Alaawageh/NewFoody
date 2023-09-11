<?php

namespace App\Http\Requests\Repository;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditRepositoryRequest extends FormRequest
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
            'qty' => 'string',
            'branch_id' => Rule::exists('branches' , 'id'),
        ];
    }
}
