<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Attribute;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateAttributeRequest;
use App\Http\Requests\Validations\UpdateAttributeRequest;

class AttributeController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.attribute');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attributes = Attribute::with('attributeType')->withCount('attributeValues')->get();

        $trashes = Attribute::onlyTrashed()->with('attributeType')->get();

        return view('admin.attribute.index', compact('attributes', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.attribute._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAttributeRequest $request)
    {
        if( Attribute::create($request->all()) ) {
            return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Display all Attribute Values the specified Attribute.
     *
     * @param  Attribute $attribute
     * @return \Illuminate\Http\Response
     */
    public function entities(Attribute $attribute)
    {
        $entities['attribute'] = $attribute;

        $entities['attributeValues'] = $attribute->attributeValues()->with('image')->get();

        $entities['trashes'] = $attribute->attributeValues()->onlyTrashed()->get();

        return view('admin.attribute.entities', $entities);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Attribute $attribute
     * @return \Illuminate\Http\Response
     */
    public function edit(Attribute $attribute)
    {
        return view('admin.attribute._edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Attribute $attribute
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        if($attribute->update($request->all())) {
            return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Attribute $attribute
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Attribute $attribute)
    {
        if($attribute->delete()) {
            return back()->with('success', trans('messages.trashed', ['model' => $this->model_name]));
        }

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
        if(Attribute::onlyTrashed()->findOrFail($id)->restore()) {
            return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $attribute = Attribute::onlyTrashed()->findOrFail($id);

        $attributeValues = $attribute->attributeValues()->get();

        foreach ($attributeValues as $entity) {
            $entity->deleteImage();
        }

        if($attribute->forceDelete()) {
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Save sorting order for attributes by ajax
     */
    public function reorder(Request $request)
    {
        foreach ($request->all() as $id => $order) {
            Attribute::findOrFail($id)->update(['order' => $order]);
        }

        return response('success!', 200);
    }

    /**
     * Response AJAX call to check if the attribute is a color/pattern type or not
     */
    public function ajaxGetParrentAttributeType(Request $request)
    {
        if ($request->ajax()){
            $type_id = Attribute::findOrFail($request->input('id'))->attribute_type_id;

            if($type_id) {
                return response("$type_id", 200);
            }
        }

        return response('Not found!', 404);
    }

}
