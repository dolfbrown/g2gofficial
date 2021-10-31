<?php

namespace App\Http\Controllers\Admin;

use App\Image;
use App\Product;
use App\ProductVariant;
use App\AttributeValue;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Validations\CreateDownloadableRequest;
use App\Http\Requests\Validations\UpdateDownloadableRequest;
// use App\Http\Requests\Validations\CreateProductVariantRequest;
use App\Common\HasVariant;

class DownloadableController extends Controller
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
        $trashes = Product::downloadable()->onlyTrashed()->with('categories', 'image')->get();

        return view('admin.downloadable.index', compact('trashes'));
    }

    // function will process the ajax request
    public function getProducts(Request $request) {

        $products = Product::downloadable()->with('image')->get();

        return Datatables::of($products)
            ->addColumn('option', function ($product) {
                return view( 'admin.partials.actions.product.options', compact('product'));
            })
            ->editColumn('image', function($product){
                return view( 'admin.partials.actions.product.image', compact('product'));
            })
            ->editColumn('title', function($product){
                return view( 'admin.partials.actions.product.name', compact('product'));
            })
            ->editColumn('price',  function ($product) {
                return view( 'admin.partials.actions.product.price', compact('product'));
            })
            ->rawColumns([ 'title', 'image', 'price', 'status', 'option' ])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.downloadable.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateDownloadableRequest $request)
    {
        $this->authorize('create', Product::class); // Check permission

        $product = Product::create($request->all());

        if ( $request->input('category_list') ) {
            $product->categories()->sync($request->input('category_list'));
        }

        if ( $request->hasFile('file') ) {
            $product->saveAttachments($request->file('file'), true);
        }

        if ( $request->input('tag_list') ) {
            $product->syncTags($product, $request->input('tag_list'));
        }

        $request->session()->flash('success', trans('messages.created', ['model' => $this->model]));

        $this->createVariants($product, $request);

        return response()->json($this->getJsonParams($product, true));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product); // Check permission

        $preview = $product->previewImages();

        return view('admin.downloadable.edit', compact('product', 'preview'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDownloadableRequest $request, Product $product)
    {
        $this->authorize('update', $product); // Check permission

        $product->update($request->all());

        $product->categories()->sync($request->input('category_list', []));

        $product->syncTags($product, $request->input('tag_list', []));

        if ( $request->hasFile('file') ) {
            $product->flushAttachments();

            $product->saveAttachments($request->file('file'), true);
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
            'redirect' => route('admin.catalog.downloadable.index')
        ];
    }
}
