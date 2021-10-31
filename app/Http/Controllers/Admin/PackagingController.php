<?php

namespace App\Http\Controllers\Admin;

use App\Packaging;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreatePackagingRequest;
use App\Http\Requests\Validations\UpdatePackagingRequest;

class PackagingController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model_name = trans('app.model.packaging');
    }

   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packagings = Packaging::with('image')->get();

        $trashes = Packaging::with('image')->onlyTrashed()->get();

        return view('admin.packaging.index', compact('packagings', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.packaging._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePackagingRequest $request)
    {
        $packaging = Packaging::create($request->all());

        if ($request->hasFile('image'))
            $packaging->saveImage($request->file('image'));

        if($packaging)
            return back()->with('error', trans('messages.failed'));

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Packaging $packaging
     * @return \Illuminate\Http\Response
     */
    public function edit(Packaging $packaging)
    {
        return view('admin.packaging._edit', compact('packaging'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Packaging $packaging
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePackagingRequest $request, Packaging $packaging)
    {
        if ( (bool) $request->input('default') ) {
            $default = \App\Packaging::where('default', 1)->first();
            if($default) $default->update(['default' => null]) ;
        }

        if( ! $packaging->update($request->all()) )
            return back()->with('error', trans('messages.failed'));

        if ($request->hasFile('image') || ($request->input('delete_image') == 1))
            $packaging->deleteImage();

        if ($request->hasFile('image'))
            $packaging->saveImage($request->file('image'));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Packaging  $packaging
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Packaging $packaging)
    {
        if($packaging->delete())
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
        if( Packaging::onlyTrashed()->findOrFail($id)->restore() )
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
        $packaging = Packaging::onlyTrashed()->findOrFail($id);

        $packaging->flushImages();

        if($packaging->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }
}