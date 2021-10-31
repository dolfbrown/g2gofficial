<?php

namespace App\Http\Controllers\Admin;

use App\Carrier;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateCarrierRequest;
use App\Http\Requests\Validations\UpdateCarrierRequest;

class CarrierController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model_name = trans('app.model.carrier');
    }

   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $carriers = Carrier::with('image', 'shippingZones')->get();

        $trashes = Carrier::with('image')->onlyTrashed()->get();

        return view('admin.carrier.index', compact('carriers', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.carrier._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCarrierRequest $request)
    {
        $carrier = Carrier::create($request->all());

        if( ! $carrier )
            return back()->with('error', trans('messages.failed'));

        if ($request->hasFile('image'))
            $carrier->saveImage($request->file('image'));

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  Carrier $carrier
     * @return \Illuminate\Http\Response
     */
    public function show(Carrier $carrier)
    {
        return view('admin.carrier._show', compact('carrier'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Carrier $carrier
     * @return \Illuminate\Http\Response
     */
    public function edit(Carrier $carrier)
    {
        return view('admin.carrier._edit', compact('carrier'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Carrier $carrier
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCarrierRequest $request, Carrier $carrier)
    {
        $carrier->update($request->all());

        if ($request->hasFile('image') || ($request->input('delete_image') == 1))
            $carrier->deleteImage();

        if ($request->hasFile('image'))
            $carrier->saveImage($request->file('image'));

        if($carrier)
            return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Carrier $carrier
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Carrier $carrier)
    {
        if($carrier->delete())
            return back()->with('success', trans('messages.trashed', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Restore the specified resource from soft delete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id)
    {
        $carrier = Carrier::onlyTrashed()->findOrFail($id);

        if($carrier->restore())
            return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $carrier = Carrier::onlyTrashed()->findOrFail($id);

        $carrier->flushImages();

        if($carrier->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }
}