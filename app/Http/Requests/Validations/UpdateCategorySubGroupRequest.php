<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateCategorySubGroupRequest extends Request
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
            'category_group_id' => 'required|integer',
            'name' =>  [
                'required',
                Rule::unique('category_sub_groups')->ignore($id),
            ],
            'slug' =>  [
                'required',
                Rule::unique('category_sub_groups')->ignore($id),
            ],
            'active' => 'required'
        ];
    }
}
