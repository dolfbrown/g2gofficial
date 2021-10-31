<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends Request
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
        $id = Request::segment(count(Request::segments())); //Current model ID

        return [
            'category_sub_group_id' => 'required',
            'name' =>  [
                'required',
                Rule::unique('categories')->ignore($id),
            ],
            'slug' =>  [
                'required',
                Rule::unique('categories')->ignore($id),
            ],
            'image' => 'mimes:jpg,jpeg,png',
            'active' => 'required'
        ];
    }
}
