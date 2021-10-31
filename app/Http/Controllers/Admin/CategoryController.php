<?php
namespace App\Http\Controllers\Admin;

use App\Category;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateCategoryRequest;
use App\Http\Requests\Validations\UpdateCategoryRequest;

class CategoryController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model_name = trans('app.model.category');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::with('subGroup:id,name,deleted_at', 'featuredImage')->withCount('products')->get();

        $trashes = Category::with('subGroup:id,name,deleted_at')->onlyTrashed()->get();

        return view('admin.category.index', compact('categories', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCategoryRequest $request)
    {
        $category = Category::create($request->all());

        if(! $category) {
            return back()->with('error', trans('messages.failed'));
        }

        if ($request->hasFile('image')) {
            $category->saveImage($request->file('image'), true);
        }

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return view('admin.category._edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Category $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        if(!$category->update($request->all())) {
            return back()->with('error', trans('messages.failed'));
        }

        if ($request->hasFile('image') || ($request->input('delete_image') == 1)) {
            $category->deleteFeaturedImage();
        }

        if ($request->hasFile('image')) {
            $category->saveImage($request->file('image'), true);
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Category $category
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Category $category)
    {
        if($category->delete()) {
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
        $category = Category::onlyTrashed()->findOrFail($id);

        if($category->restore()) {
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
        $category = Category::onlyTrashed()->findOrFail($id);

        $category->flushImages();

        if($category->forceDelete()) {
            return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }
}