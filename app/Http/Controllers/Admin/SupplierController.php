<?php
namespace App\Http\Controllers\Admin;

use App\Supplier;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateSupplierRequest;
use App\Http\Requests\Validations\UpdateSupplierRequest;

class SupplierController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.supplier');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::with('primaryAddress', 'image')->get();

        $trashes = Supplier::with('image')->onlyTrashed()->get();

        return view('admin.supplier.index', compact('suppliers', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.supplier._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSupplierRequest $request)
    {
        $supplier = Supplier::create($request->all());

        if( ! $supplier )
            return back()->with('error', trans('messages.failed'));

        $this->saveAdrress($request->all(), $supplier);

        if ($request->hasFile('image'))
            $supplier->saveImage($request->file('image'));

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        return view('admin.supplier._show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.supplier._edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        if( ! $supplier->update($request->all()) )
            return back()->with('error', trans('messages.failed'));

        if ($request->hasFile('image') || ($request->input('delete_image') == 1))
            $supplier->deleteImage();

        if ($request->hasFile('image'))
            $supplier->saveImage($request->file('image'));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Supplier $supplier)
    {
        if($supplier->delete())
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
        $supplier = Supplier::onlyTrashed()->findOrFail($id);

        if($supplier->restore())
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
        $supplier = Supplier::onlyTrashed()->findOrFail($id);

        $supplier->flushAddresses();

        $supplier->flushImages();

        if($supplier->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * [saveAdrress]
     *
     * @param  array    $address
     * @param  Supplier $supplier
     */
    private function saveAdrress(array $address, Supplier $supplier)
    {
        return $supplier->addresses()->create($address);
    }
}