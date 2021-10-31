<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class CreateDownloadableRequest extends Request
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
        $this->merge([
            'user_id' => $this->user()->id,
            'downloadable' => true,
        ]);

        return [
            'category_list' => 'required',
            'title' => 'required',
            'sku' => 'required|unique:products',
            'slug' => 'required|unique:products',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|lt:price',
            'available_from' => 'nullable|date',
            'offer_start' => 'nullable|date|required_with:offer_price',
            'offer_end' => 'nullable|date|required_with:offer_price|after:offer_start',
            'active' => 'required',
            'file' => 'required',
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
            'file.required' => trans('validation.downloadable_required'),
            'category_list.required' => trans('validation.category_list_required'),
        ];
    }
}
