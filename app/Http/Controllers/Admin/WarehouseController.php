<?php
namespace App\Http\Controllers\Admin;

use App\Warehouse;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateWarehouseRequest;
use App\Http\Requests\Validations\UpdateWarehouseRequest;

class WarehouseController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.warehouse');
    }

   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $warehouses = Warehouse::with('manager', 'image', 'primaryAddress')->get();

        $trashes = Warehouse::with('image')->onlyTrashed()->get();

        return view('admin.warehouse.index', compact('warehouses', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.warehouse._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateWarehouseRequest $request)
    {
        $warehouse = Warehouse::create($request->all());

        if( ! $warehouse )
            return back()->with('error', trans('messages.failed'));

        $warehouse->addresses()->create($request->all());

        if ($request->hasFile('image'))
            $warehouse->saveImage($request->file('image'));

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  Warehouse $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        return view('admin.warehouse._show', compact('warehouse'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Warehouse $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Warehouse $warehouse)
    {
        return view('admin.warehouse._edit', compact('warehouse'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Warehouse $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        if( ! $warehouse->update($request->all()) )
            return back()->with('error', trans('messages.failed'));

        if ($request->hasFile('image') || ($request->input('delete_image') == 1))
            $warehouse->deleteImage();

        if ($request->hasFile('image'))
            $warehouse->saveImage($request->file('image'));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Warehouse $warehouse
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Warehouse $warehouse)
    {
        if($warehouse->delete())
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
        $warehouse = Warehouse::onlyTrashed()->findOrFail($id);

        if($warehouse->restore())
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
        $warehouse = Warehouse::onlyTrashed()->findOrFail($id);

        $warehouse->flushAddresses();

        $warehouse->flushImages();

       if($warehouse->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }
}