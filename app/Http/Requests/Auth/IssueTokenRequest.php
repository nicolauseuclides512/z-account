<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class IssueTokenRequest extends FormRequest
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
            'username' => 'required|email',
            'application' => 'required|string|in:inventory,invoice',
            'grant_type' => 'required|string|in:password,authorization_code,refresh_token,client_credentials',
            'client_id' => 'required|integer|in:1,2',
            'client_secret' => 'required|string',
            'password' => 'required|string'
        ];
    }
}
