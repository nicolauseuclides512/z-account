<?php

namespace App\Http\Requests\Gateway\Stores\Items;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @author Jehan Afwazi Ahmad <jehan.afwazi@gmail.com>.
 */
class ItemStoreRequest extends FormRequest
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
//            'images.*' => 'mimes:jpeg,jpg,png|max:1024',
            'uom_id' => 'required|integer',
            'tax_id' => 'nullable|integer',
            'item_name' => 'required|string|max:100',
//            'item_attributes' => 'nullable|string',
            'weight' => 'sometimes|nullable|integer|min:1',
            'weight_unit' => 'sometimes|nullable|string|in:gr,kg',
            'dimension_l' => 'integer|min:0',
            'dimension_w' => 'integer|min:0',
            'dimension_h' => 'integer|min:0',
            'code_sku' => 'nullable|string|max:50',
            'sales_rate' => 'numeric|between:0,9999999999',
            'parent_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'compare_rate' => 'numeric|between:0,9999999999',
            'barcode' => 'nullable|string',
            'page_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
//            'slug' => 'string',
//            'visibility' => 'nullable|string',
            'tags' => 'nullable|string',
            'stock_quantity' => 'numeric|nullable',
        ];
    }
}
