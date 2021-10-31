<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends Request
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

        $rules = [];
        $rules['name'] = [
                                'required',
                                Rule::unique('roles')->ignore($id),
                            ];

        if (Request::user()->accessLevel())
            $rules['level'] = 'nullable|integer|between:'.Request::user()->accessLevel().','.config('system_settings.max_role_level');

        if (Request::input('level') && !Request::user()->accessLevel())
            Request::replace(['level' => Null]); //Reset the level

        return $rules;
    }
}
