<?php

namespace App\Http\Requests\Validations;

use Illuminate\Validation\Rule;
use App\Http\Requests\Request;

class UpdateCategoryGroupRequest extends Request
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
            'name' =>  [
                'required',
                Rule::unique('category_groups')->ignore($id),
            ],
            'slug' =>  [
                'required',
                Rule::unique('category_groups')->ignore($id),
            ],
            'active' => 'required'
        ];
    }
}
