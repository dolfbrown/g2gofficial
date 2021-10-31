<?php

namespace App\Http\Controllers\Admin;

use App\CategorySubGroup;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateCategorySubGroupRequest;
use App\Http\Requests\Validations\UpdateCategorySubGroupRequest;

class CategorySubGroupController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model_name = trans('app.model.category_group');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categorySubGrps = CategorySubGroup::with('group:id,name,deleted_at')->withCount('categories')->get();

        $trashes = CategorySubGroup::with('group:id,name,deleted_at')->onlyTrashed()->get();

        return view('admin.category.categorySubGroup', compact('categorySubGrps', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category._createSubGrp');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCategorySubGroupRequest $request)
    {
        $categoryGroup = CategorySubGroup::create($request->all());

        if(!$categoryGroup) {
            return back()->with('error', trans('messages.failed'));
        }

        if ($request->hasFile('image')) {
            $categoryGroup->saveImage($request->file('image'), true);
        }

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  CategorySubGroup  $categorySubGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(CategorySubGroup $categorySubGroup)
    {
        return view('admin.category._editSubGrp', compact('categorySubGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  CategorySubGroup $categorySubGroup
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategorySubGroupRequest $request, CategorySubGroup $categorySubGroup)
    {
        if(!$categorySubGroup->update($request->all())) {
            return back()->with('error', trans('messages.failed'));
        }

        if ($request->hasFile('image') || ($request->input('delete_image') == 1)) {
            $categorySubGroup->deleteFeaturedImage();
        }

        if ($request->hasFile('image')) {
            $categorySubGroup->saveImage($request->file('image'), true);
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  CategorySubGroup $categorySubGroup
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, CategorySubGroup $categorySubGroup)
    {
        if($categorySubGroup->delete()) {
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
        $categorySubGroup = CategorySubGroup::onlyTrashed()->findOrFail($id);

        if($categorySubGroup->restore()) {
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
        $categorySubGroup = CategorySubGroup::onlyTrashed()->findOrFail($id);

        $categorySubGroup->flushImages();

        if($categorySubGroup->forceDelete()) {
            return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }
}