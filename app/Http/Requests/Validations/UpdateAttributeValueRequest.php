<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateAttributeValueRequest extends Request
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
        $ignore = Request::segment(count(Request::segments())); //Current model ID

        return [
           'attribute_id' => 'required',
           'value' => [
                'required',
                Rule::unique('attribute_values')->ignore($ignore),
            ],
           'image' => 'mimes:jpeg,png',
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
            'attribute_id.required' => trans('validation.attribute_id_required'),
            'value.required' => trans('validation.attribute_value_required'),
        ];
    }
}
