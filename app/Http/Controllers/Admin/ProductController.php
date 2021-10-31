<?php

namespace App\Http\Controllers\Admin;

// use App\Image;
use App\Product;
// use App\ProductVariant;
use App\Common\HasVariant;
// use App\AttributeValue;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Validations\CreateProductRequest;
use App\Http\Requests\Validations\UpdateProductRequest;
use App\Http\Requests\Validations\CreateProductVariantRequest;

class ProductController extends Controller
{
    use Authorizable, HasVariant;

    private $model;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = trans('app.model.product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trashes = Product::physical()->onlyTrashed()->with('categories', 'image')->get();

        return view('admin.product.index', compact('trashes'));
    }

    // function will process the ajax request
    public function getProducts(Request $request) {

        $products = Product::physical()->with('image', 'image')->get();

        return Datatables::of($products)
            ->addColumn('option', function ($product) {
                return view( 'admin.partials.actions.product.options', compact('product'));
            })
            ->editColumn('title', function($product){
                return view( 'admin.partials.actions.product.name', compact('product'));
            })
            ->editColumn('image', function($product){
                return view( 'admin.partials.actions.product.image', compact('product'));
            })
            // ->editColumn('gtin', function($product){
            //     return view( 'admin.partials.actions.product.gtin', compact('product'));
            // })
            ->editColumn('quantity',  function ($product) {
                return view( 'admin.partials.actions.product.quantity', compact('product'));
            })
            ->editColumn('price',  function ($product) {
                return view( 'admin.partials.actions.product.price', compact('product'));
            })
            // ->editColumn('category',  function ($product) {
            //     return view( 'admin.partials.actions.product.category', compact('product'));
            // })
            ->rawColumns([ 'title', 'image', 'quantity', 'price', 'status', 'option' ])
            // ->rawColumns([ 'name', 'gtin', 'category', 'status', 'option' ])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductRequest $request)
    {
        $this->authorize('create', Product::class); // Check permission

        $product = Product::create($request->all());

        if ($request->input('category_list')) {
            $product->categories()->sync($request->input('category_list'));
        }

        if ($request->input('tag_list')) {
            $product->syncTags($product, $request->input('tag_list'));
        }

        if ($request->hasFile('image')) {
            $product->saveImage($request->file('image'), true);
        }

        $request->session()->flash('success', trans('messages.created', ['model' => $this->model]));

        $this->createVariants($product, $request);

        return response()->json($this->getJsonParams($product, true));
    }

    /**
     * Display the specified resource.
     *
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $this->authorize('view', $product); // Check permission

        return view('admin.product._show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product); // Check permission

        $preview = $product->previewImages();

        return view('admin.product.edit', compact('product', 'preview'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function editQtt(Product $product)
    {
        $this->authorize('update', $product); // Check permission

        return view('admin.product._editQtt', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function updateQtt(Request $request, Product $product)
    {
        $product->update(['stock_quantity' => $request->input('stock_quantity')]);

        return response("success", 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product); // Check permission

        $product->update($request->all());

        $product->categories()->sync($request->input('category_list', []));

        $product->syncTags($product, $request->input('tag_list', []));

        if ($request->hasFile('file') || ($request->input('delete_image') == 1)){
            if($product->featuredImage) {
                $product->deleteImage($product->featuredImage);
            }
        }

        if ($request->hasFile('file')) {
            $product->saveImage($request->file('file'), true);
        }

        if($product->variants->count() > 0) {
            $this->updateVariants($product, $request);
        }
        else {
            $this->createVariants($product, $request);
        }

        $request->session()->flash('success', trans('messages.updated', ['model' => $this->model]));

        return response()->json($this->getJsonParams($product));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Product $product)
    {
        if($product->delete()) {
            return back()->with('success', trans('messages.trashed', ['model' => $this->model]));
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
        $product = Product::onlyTrashed()->findOrFail($id);

        if($product->restore()) {
            return back()->with('success', trans('messages.restored', ['model' => $this->model]));
        }

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
        $product = Product::onlyTrashed()->findOrFail($id);

        $product->detachTags($product->id, 'product');

        $product->flushImages();

        $product->flushFeedbacks();

        $product->flushAttachments();

        if($product->forceDelete()) {
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model]));
        }

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCombinations(Request $request)
    {
        $variants = $this->confirmAttributes($request->except('_token','_'));

        $combinations = generate_combinations($variants);

        return view('admin.product._combinations', compact('combinations'));
    }

    /**
     * return json params to procceed the form
     *
     * @param  Product $product
     * @param  bool $variant
     *
     * @return array
     */
    private function getJsonParams(Product $product, $variant = false){
        return [
            'id' => $product->id,
            'model' => 'product',
            'redirect' => route('admin.catalog.product.index')
        ];
    }


}
