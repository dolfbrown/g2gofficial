<?php
namespace App\Http\Controllers\Admin;

use App\CategoryGroup;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateCategoryGroupRequest;
use App\Http\Requests\Validations\UpdateCategoryGroupRequest;

class CategoryGroupController extends Controller
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
        $categoryGrps = CategoryGroup::withCount('subGroups')->with('image')->orderBy('order', 'asc')->get();

        $trashes = CategoryGroup::onlyTrashed()->withCount('subGroups')->get();

        return view('admin.category.categoryGroup', compact('categoryGrps', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category._createGrp');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCategoryGroupRequest $request)
    {
        $categoryGroup = CategoryGroup::create($request->all());

        if(! $categoryGroup){
            return back()->with('error', trans('messages.failed'));
        }

        if ($request->hasFile('image')){
            $categoryGroup->saveImage($request->file('image'), true);
        }

        if ($request->hasFile('bg_image')){
            $categoryGroup->saveImage($request->file('bg_image'));
        }

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  CategoryGroup $categoryGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryGroup $categoryGroup)
    {
        return view('admin.category._editGrp', compact('categoryGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  CategoryGroup $categoryGroup
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryGroupRequest $request, CategoryGroup $categoryGroup)
    {
        if( config('app.demo') == true && $categoryGroup->id <= config('system.demo.category_groups', 9) ) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        if(!$categoryGroup->update($request->all())) {
            return back()->with('error', trans('messages.failed'));
        }

        if ($request->hasFile('image') || ($request->input('delete_image') == 1)){
            if($categoryGroup->featuredImage) {
                $categoryGroup->deleteImage($categoryGroup->featuredImage);
            }
        }

        if ($request->hasFile('image')) {
            $categoryGroup->saveImage($request->file('image'), true);
        }

        if ($request->hasFile('bg_image') || ($request->input('delete_bg_image') == 1)){
            if($categoryGroup->images->first())
                $categoryGroup->deleteImage($categoryGroup->images->first());
        }

        if ($request->hasFile('bg_image')) {
            $categoryGroup->saveImage($request->file('bg_image'));
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  CategoryGroup $categoryGroup
     * @return \Illuminate\Http\Response
     */
    public function trash(CategoryGroup $categoryGroup)
    {
        if(config('app.demo') == true && $categoryGroup->id <= config('system.demo.category_groups', 9)) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        if($categoryGroup->delete()) {
            return back()->with('success', trans('messages.trashed', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Restore the specified resource from soft delete.
     *
     * @param  int $catGrp
     * @return \Illuminate\Http\Response
     */
    public function restore($catGrp)
    {
        $categoryGroup = CategoryGroup::onlyTrashed()->findOrFail($catGrp);

        if($categoryGroup->restore()){
            return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $catGrp
     * @return \Illuminate\Http\Response
     */
    public function destroy($catGrp)
    {
        $categoryGroup = CategoryGroup::onlyTrashed()->findOrFail($catGrp);

        $categoryGroup->flushImages();

        if($categoryGroup->forceDelete()){
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }
}