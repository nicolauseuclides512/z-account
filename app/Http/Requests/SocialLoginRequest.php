<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends FormRequest
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
            'token' => 'required|string',
            'client_id' => 'required|string',
            'provider' => 'required|string|in:google,facebook,twitter',
            'application' => 'required|string|in:inventory,invoice',
            'application_cli' => 'required|string|in:zuragan_pos_mobile'
        ];
    }
}
