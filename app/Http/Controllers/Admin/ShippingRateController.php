<?php
namespace App\Http\Controllers\Admin;

use App\ShippingRate;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateShippingRateRequest;
use App\Http\Requests\Validations\UpdateShippingRateRequest;

class ShippingRateController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.shipping_rate');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int $shippingZone
     * @param  str $basedOn
     * @return \Illuminate\Http\Response
     */
    public function create($shippingZone, $basedOn = 'price')
    {
        return view('admin.shipping_rate._create', compact('shippingZone', 'basedOn'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateShippingRateRequest $request)
    {
        if(ShippingRate::create($request->all()))
            return back()->with('success', trans('messages.created', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  ShippingRate $shippingRate
     * @return \Illuminate\Http\Response
     */
    public function edit(ShippingRate $shippingRate)
    {
        return view('admin.shipping_rate._edit', ['shipping_rate' => $shippingRate]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ShippingRate $shippingRate
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateShippingRateRequest $request, ShippingRate $shippingRate)
    {
        if($shippingRate->update($request->all()))
            return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ShippingRate $shippingRate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ShippingRate $shippingRate)
    {
        if($shippingRate->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }
}