<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends Request
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
        $id = $this->route('product');
        incevioAutoloadHelpers(getMysqliConnection());

        return [
            'category_list' => 'required',
            'title' => 'required',
            'sku' => [
                'required',
                Rule::unique('products')->ignore($id),
            ],
            'slug' => [
                'required',
                Rule::unique('products')->ignore($id),
            ],
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|lt:price',
            'available_from' => 'nullable|date',
            'offer_start' => 'nullable|date|required_with:offer_price',
            'offer_end' => 'nullable|date|required_with:offer_price|after:offer_start',
            'active' => 'required',
            'image' => 'mimes:jpg,jpeg,png,gif',
        ];
    }

   /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'category_list.required' => trans('validation.category_list_required'),
        ];
    }
}
