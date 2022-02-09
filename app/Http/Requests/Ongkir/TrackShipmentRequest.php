<?php

namespace App\Http\Requests\Ongkir;

use Illuminate\Foundation\Http\FormRequest;

class TrackShipmentRequest extends FormRequest
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
            'waybill' => 'required|string',
            'courier' => 'required|string'
        ];
    }
}
