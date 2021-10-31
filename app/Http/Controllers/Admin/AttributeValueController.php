<?php

namespace App\Http\Controllers\Admin;

use App\Attribute;
use App\AttributeValue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateAttributeValueRequest;
use App\Http\Requests\Validations\UpdateAttributeValueRequest;

class AttributeValueController extends Controller
{
    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.attribute_value');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $attribute = $id ? Attribute::find($id) : null;

        return view('admin.attribute-value._create', compact('attribute'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAttributeValueRequest $request)
    {
        $attributeValue = AttributeValue::create($request->all());

        if( ! $attributeValue )
            return back()->with('error', trans('messages.failed'));

       if ($request->hasFile('image'))
            $attributeValue->saveImage($request->file('image'));

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display all Attribute Values the specified Attribute.
     *
     * @param  AttributeValue  $attributeValue
     * @return \Illuminate\Http\Response
     */
    public function show(AttributeValue $attributeValue)
    {
        return view('admin.attribute-value._show', compact('attributeValue'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  AttributeValue $attributeValue
     * @return \Illuminate\Http\Response
     */
    public function edit(AttributeValue $attributeValue)
    {
        $this->authorize('update', $attributeValue);

        $attribute = Attribute::findOrFail($attributeValue->attribute_id);

        return view('admin.attribute-value._edit', compact('attributeValue', 'attribute'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  AttributeValue $attributeValue
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAttributeValueRequest $request, AttributeValue $attributeValue)
    {
        $this->authorize('update', $attributeValue);

        if ($request->hasFile('image') || ($request->input('delete_image') == 1)) {
            $attributeValue->deleteImage();
        }

        if ($request->hasFile('image')) {
            $attributeValue->saveImage($request->file('image'));
        }

        if($attributeValue->update($request->all())) {
            return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  AttributeValue $attributeValue
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, AttributeValue $attributeValue)
    {
        $this->authorize('delete', $attributeValue);

        if($attributeValue->delete())
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
        if(AttributeValue::onlyTrashed()->findOrFail($id)->restore())
            return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));

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
        $attributeValue = AttributeValue::onlyTrashed()->findOrFail($id);

        $attributeValue->flushImages();

        if($attributeValue->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Save sorting order for attributes by ajax
     */
    public function reorder(Request $request)
    {
        foreach ($request->all() as $id => $order)
            AttributeValue::findOrFail($id)->update(['order' => $order]);

        return response('success!', 200);
    }
}