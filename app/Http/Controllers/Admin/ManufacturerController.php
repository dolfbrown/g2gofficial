<?php
namespace App\Http\Controllers\Admin;

use App\Manufacturer;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
// use App\Repositories\Manufacturer\ManufacturerRepository;
use App\Http\Requests\Validations\CreateManufacturerRequest;
use App\Http\Requests\Validations\UpdateManufacturerRequest;

class ManufacturerController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.manufacturer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $manufacturers = Manufacturer::with('country', 'logo')->withCount('products')->get();

        $trashes = Manufacturer::with('logo')->onlyTrashed()->get();

        return view('admin.manufacturer.index', compact('manufacturers', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.manufacturer._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateManufacturerRequest $request)
    {
        $manufacturer =  Manufacturer::create($request->all());

        if( ! $manufacturer )
            return back()->with('error', trans('messages.failed'));

        if ($request->hasFile('image'))
            $manufacturer->saveImage($request->file('image'));

        if ($request->hasFile('cover_image'))
            $manufacturer->saveImage($request->file('cover_image'), true);

       return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  Manufacturer $manufacturer
     * @return \Illuminate\Http\Response
     */
    public function show(Manufacturer $manufacturer)
    {
        return view('admin.manufacturer._show', compact('manufacturer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Manufacturer  $manufacturer
     * @return \Illuminate\Http\Response
     */
    public function edit(Manufacturer $manufacturer)
    {
        return view('admin.manufacturer._edit', compact('manufacturer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Manufacturer $manufacturer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateManufacturerRequest $request, Manufacturer $manufacturer)
    {
        $manufacturer->update($request->all());

        if ($request->hasFile('image') || ($request->input('delete_image') == 1))
            $manufacturer->deleteLogo();
        if ($request->hasFile('image'))
            $manufacturer->saveImage($request->file('image'));

        if ($request->hasFile('cover_image') || ($request->input('delete_cover_image') == 1))
            $manufacturer->deleteFeaturedImage();
        if ($request->hasFile('cover_image'))
            $manufacturer->saveImage($request->file('cover_image'), true);

        if($manufacturer)
            return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Manufacturer $manufacturer
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Manufacturer $manufacturer)
    {
        if($manufacturer->delete())
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
        $manufacturer = Manufacturer::onlyTrashed()->findOrFail($id);

        if($manufacturer->restore())
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
        $manufacturer = Manufacturer::onlyTrashed()->findOrFail($id);

        $manufacturer->flushImages();

        if($manufacturer->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

}
