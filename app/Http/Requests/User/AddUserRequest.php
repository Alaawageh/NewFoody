<?php

namespace App\Http\Requests\User;

use App\Types\UserTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddUserRequest extends FormRequest
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
            'email' => ['required' , 'email' , Rule::exists('users' , 'email')],
            'password' => 'required|min:8|max:24',
            // 'user_type' => [UserTypes::KKITCHEN,UserTypes::CASHER]
        ];
    }
}
