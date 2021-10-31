<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class CreateShippingZoneRequest extends Request
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
        incevioAutoloadHelpers(getMysqliConnection());
        if ($this->has('rest_of_the_world')) {
            Request::merge(['state_ids' => [], 'country_ids' => []]);
        }else if($this->has('country_ids')){
            $state_ids = get_states_of($this->input('country_ids'));
            Request::merge(['state_ids' => array_keys($state_ids)]);
        }

        return [
           'name' => 'bail|required|unique:shipping_zones',
           'tax_id' => 'required',
           'country_ids' => 'required_unless:rest_of_the_world,1',
           'rest_of_the_world' => 'bail|sometimes|nullable|unique:shipping_zones',
           'active' => 'required|boolean',
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
            'country_ids.required_unless' => trans('validation.shipping_zone_country_ids_required'),
            'tax_id.required' => trans('validation.shipping_zone_tax_id_required'),
            'rest_of_the_world.composite_unique' => trans('validation.rest_of_the_world_composite_unique'),
        ];
    }
}