<?php

namespace App\Http\Requests\User\Auth;

use App\Http\Requests\ApiBaseRequest;

class RegisterRequest extends ApiBaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|min:8|max:25|confirmed',
            'cnpj' => 'required|max:20|string|unique:companies,cnpj',
            "company_name" => 'required|string|max:255'
        ];
    }
}
