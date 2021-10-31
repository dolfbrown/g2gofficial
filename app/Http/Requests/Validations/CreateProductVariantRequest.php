<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class CreateProductVariantRequest extends Request
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
        $this->merge(['user_id' => $this->user()->id]);

        return [
            'sku' => 'required|unique:product_variants',
            'attributes.*' => 'required',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|lt:price',
            'available_from' => 'nullable|date',
            'offer_start' => 'nullable|date',
            'offer_end' => 'nullable|date|after:offer_start',
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
        $messages =  [
            'attributes.*.required' => trans('validation.attributes_required'),
            'offer_start.after' => trans('validation.offer_start_after'),
            'offer_end.required_with' => trans('validation.offer_end_required'),
            'offer_end.after' => trans('validation.offer_end_after'),
        ];

        foreach($this->request->get('attributes') as $key => $val){
            $messages['attributes.'.$key.'.required'] = $val .' '. trans('validation.attributes_required');
        }

        return $messages;
    }
}
