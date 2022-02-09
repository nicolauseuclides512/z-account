<?php

namespace App\Http\Requests\Gateway\Stores\Items;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @author Jehan Afwazi Ahmad <jehan.afwazi@gmail.com>.
 */
class AddImageItemRequest extends FormRequest
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
            'image' => 'mimes:jpeg,jpg,png|max:1024',
        ];
    }
}
