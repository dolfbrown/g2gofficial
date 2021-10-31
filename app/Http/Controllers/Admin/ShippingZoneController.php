<?php
namespace App\Http\Controllers\Admin;

use App\ShippingZone;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateShippingZoneRequest;
use App\Http\Requests\Validations\UpdateShippingZoneRequest;

class ShippingZoneController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.shipping_zone');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shipping_zones = ShippingZone::with('rates')->get();

        return view('admin.shipping_zone.index', compact('shipping_zones'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.shipping_zone._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateShippingZoneRequest $request)
    {
        if(ShippingZone::create($request->all()))
            return back()->with('success', trans('messages.created', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  ShippingZone $shippingZone
     * @return \Illuminate\Http\Response
     */
    public function edit(ShippingZone $shippingZone)
    {
        return view('admin.shipping_zone._edit', ['shipping_zone' => $shippingZone]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ShippingZone $shippingZone
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateShippingZoneRequest $request, ShippingZone $shippingZone)
    {
        if ($request->has('rest_of_the_world') && $request->input('rest_of_the_world') == 1) {
            $request->merge(['state_ids' => [], 'country_ids' => []]);
        }
        else{
            $state_ids = [];
            if($request->has('country_ids')){
                $country_ids = $request->input('country_ids');
                $old_country_ids = $shippingZone->country_ids; //Current values

                $kept_country_ids = array_intersect($old_country_ids, $country_ids); //Unchanged countries
                $temp_states = get_states_of($kept_country_ids); //All states of unchanged countries
                $kept_state_ids = array_intersect($shippingZone->state_ids, array_keys($temp_states)); //States what will keep unchange

                $new_country_ids = array_diff($country_ids, $old_country_ids); //If there is new countries
                $new_state_ids = get_states_of($new_country_ids); //States of new countries

                $state_ids = array_merge($kept_state_ids, array_keys($new_state_ids)); //Creating new and updated values
            }
            $request->merge(['state_ids' => $state_ids]);
        }

        if($shippingZone->update($request->all()))
            return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ShippingZone $shippingZone
     * @param  int  $country
     * @return \Illuminate\Http\Response
     */
    public function removeCountry(Request $request, ShippingZone $shippingZone, $country)
    {
        //Remove state ids of the country
        $old_states = $shippingZone->state_ids;
        $states = get_states_of($country);
        $state_ids = array_diff($old_states, array_keys($states));

        //Remove country id
        $country_ids = $shippingZone->country_ids;
        $find = array_search($country, $country_ids);
        unset($country_ids[$find]);

        //Save the new values
        $shippingZone->country_ids = $country_ids;
        $shippingZone->state_ids = $state_ids;

        if($shippingZone->save())
            return back()->with('success',  trans('messages.updated', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Update the specified resource from storage.
     *
     * @param  ShippingZone $shippingZone
     * @param  int  $country
     * @return \Illuminate\Http\Response
     */
    public function editStates(ShippingZone $shippingZone, $country)
    {
        return view('admin.shipping_zone._states', compact('shippingZone', 'country'));
    }

    /**
     * Update the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ShippingZone $shippingZone
     * @param  int  $country
     * @return \Illuminate\Http\Response
     */
    public function updateStates(Request $request, ShippingZone $shippingZone, $country)
    {
        //Remove all state ids of the country
        $states = get_states_of($country);
        $temp_state_ids = array_diff($shippingZone->state_ids, array_keys($states));

        //Creating new and updated values
        $state_ids = array_merge($temp_state_ids, $request->input('states'));

        $shippingZone->state_ids = $state_ids;

        if($shippingZone->save())
            return back()->with('success',  trans('messages.updated', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ShippingZone $shippingZone
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ShippingZone $shippingZone)
    {
        if($shippingZone->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Return tax rate
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function ajaxGetTaxRate(Request $request)
    {
        if ($request->ajax()){
            $taxrate = getTaxRate($request->input('ID'));

            return get_formated_decimal($taxrate, true, 2);
        }

        return false;
    }
}