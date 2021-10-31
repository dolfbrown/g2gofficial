<?php
namespace App\Http\Controllers\Admin;

use Auth;
use App\Customer;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateCustomerRequest;
use App\Http\Requests\Validations\UpdateCustomerRequest;

class CustomerController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.customer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $customers = Customer::with('image', 'primaryAddress')->withCount('orders');

        $trashes = Customer::with('image')->onlyTrashed()->get();

        return view('admin.customer.index', compact('trashes'));
    }

    // function will process the ajax request
    public function getCustomers(Request $request) {

        $customers = Customer::with('image', 'primaryAddress')->withCount('orders');

        return Datatables::of($customers)
            ->addColumn('option', function ($customer) {
                return view( 'admin.partials.actions.customer.options', compact('customer'));
            })
            ->editColumn('nice_name',  function ($customer) {
                return view( 'admin.partials.actions.customer.nice_name', compact('customer'));
            })
            ->editColumn('name', function($customer){
                return view( 'admin.partials.actions.customer.full_name', compact('customer'));
            })
            ->editColumn('orders_count', function($customer){
                return view( 'admin.partials.actions.customer.orders_count', compact('customer'));
            })
            ->rawColumns([ 'nice_name', 'name', 'orders_count', 'option' ])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.customer._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCustomerRequest $request)
    {
        $customer = Customer::create($request->all());

        if( ! $customer )
            return back()->with('error', trans('messages.failed'));

        $this->saveAdrress($request->all(), $customer);

        if ($request->hasFile('image'))
            $customer->saveImage($request->file('image'));

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return view('admin.customer._show', compact('customer'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function addresses(Customer $customer)
    {
        $addresses = $customer->addresses()->get();

        return view('address.show', compact('customer', 'addresses'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function profile(Customer $customer)
    {
        return view('admin.customer.profile', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('admin.customer._edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        if( config('app.demo') == true && $id <= config('system.demo.customers', 1) )
            return back()->with('warning', trans('messages.demo_restriction'));

        if( ! $customer->update($request->all()) )
            return back()->with('error', trans('messages.failed'));

        if ($request->hasFile('image') || ($request->input('delete_image') == 1))
            $customer->deleteImage();

        if ($request->hasFile('image'))
            $customer->saveImage($request->file('image'));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Customer $customer)
    {
        if( config('app.demo') == true && $id <= config('system.demo.customers', 1) )
            return back()->with('warning', trans('messages.demo_restriction'));

        if($customer->delete())
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
        $customer = Customer::onlyTrashed()->findOrFail($id);

        if($customer->restore())
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
        $customer = Customer::onlyTrashed()->findOrFail($id);

        $customer->flushAddresses();

        $customer->flushImages();

        if($customer->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    private function saveAdrress(array $address, $customer)
    {
        $customer->addresses()->create($address);
    }
}
