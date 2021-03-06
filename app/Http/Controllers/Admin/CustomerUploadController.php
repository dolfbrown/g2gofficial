<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Customer;
use App\State;
use App\Helpers\ListHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Http\Requests\Validations\CustomerUploadRequest;
use App\Http\Requests\Validations\CustomerImportRequest;

class CustomerUploadController extends Controller
{

	private $failed_list = [];

	/**
	 * Show upload form
	 *
     * @return \Illuminate\Http\Response
	 */
	public function showForm()
	{
        return view('admin.customer._upload_form');
	}

	/**
	 * Upload the csv file and generate the review table
	 *
	 * @param  CustomerUploadRequest $request
     * @return \Illuminate\Http\Response
	 */
	public function upload(CustomerUploadRequest $request)
	{
		$path = $request->file('customers')->getRealPath();

		$records = array_map('str_getcsv', file($path));

	    // Validations check for csv_import_limit
	    if( (count($records) - 1) > get_csv_import_limit() ){
	    	$err = (new MessageBag)->add('error', trans('validation.upload_rows', ['rows' => get_csv_import_limit()]));

	    	return back()->withErrors($err);
	    }

	    // Get field names from header column
		$fields = array_map('strtolower', $records[0]);

	    // Remove the header column
	    array_shift($records);

	    $rows = [];
	    foreach ($records as $record) {
	    	// Trim the inputes
    		$trimed = array_map('trim', $record);

	    	// Set the field name as key
			$temp = array_combine($fields, $trimed);

			// Get the clean data
	    	$rows[] = clear_encoding_str($temp);
	    }

        return view('admin.customer.upload_review', compact('rows'));
	}

	/**
	 * Perform import action
	 *
	 * @param  CustomerImportRequest $request
     * @return \Illuminate\Http\Response
	 */
	public function import(CustomerImportRequest $request)
	{
        if( config('app.demo') == TRUE )
            return redirect()->route('admin.admin.customer.index')->with('warning', trans('messages.demo_restriction'));

		// Reset the Failed list
		$this->failed_list = [];

		foreach ($request->input('data') as $row) {
			$data = unserialize($row);

			// Ignore if the name field is not given
			if( ! $data['full_name'] || ! $data['email'] ){
				$reason = $data['full_name'] ? trans('help.email_field_required') : trans('help.name_field_required');
				$this->pushIntoFailed($data, $reason);
				continue;
			}

			// Validate email address
			if( ! filter_var($data['email'], FILTER_VALIDATE_EMAIL) ){
				$this->pushIntoFailed($data, trans('help.invalid_email'));
				continue;
			}

			// Ignore if the email is exist in the database
			$customer = Customer::select('email')->where('email', $data['email'])->first();
			if( $customer ){
				$this->pushIntoFailed($data, trans('help.email_already_exist'));
				continue;
			}

			// Create the customer and get it, If failed then insert into the ignored list
			if( ! $this->createCustomer($data) ){
				$this->pushIntoFailed($data, trans('help.input_error'));
				continue;
			}
		}

        $request->session()->flash('success', trans('messages.imported', ['model' => trans('app.customers')]));

        $failed_rows = $this->getFailedList();

		if(!empty($failed_rows)) {
	        return view('admin.customer.import_failed', compact('failed_rows'));
		}

        return redirect()->route('admin.admin.customer.index');
	}

	/**
	 * Create Product
	 *
	 * @param  array $product
	 * @return App\Product
	 */
	private function createCustomer($data)
	{
		// Create the product
		$customer = Customer::create([
						'name' => $data['full_name'],
						'nice_name' => $data['nice_name'],
						'email' => $data['email'],
						'password' => $data['temporary_password'],
						'description' => $data['description'],
						'sex' => 'app.' . strtolower($data['sex']),
						'dob' => date('Y-m-d', strtotime($data['dob'])),
						'accepts_marketing' => strtoupper($data['accepts_marketing']) == 'TRUE' ? 1 : 0,
						'active' => strtoupper($data['active']) == 'TRUE' ? 1 : 0,
					]);

		// Create addresses
		if($data['primary_address_line_1'])
			$customer->primaryAddress()->create($this->makeAddress($data, 'primary'));
		if($data['billing_address_line_1'])
			$customer->billingAddress()->create($this->makeAddress($data, 'billing'));
		if($data['shipping_address_line_1'])
			$customer->shippingAddress()->create($this->makeAddress($data, 'shipping'));

		// Upload featured image
        if ($data['avatar_link'])
            $customer->saveImageFromUrl($data['avatar_link']);

		return $customer;
	}

	/**
	 * downloadTemplate
	 *
	 * @return response response
	 */
	public function downloadTemplate()
	{
		$pathToFile = public_path("csv_templates/customers.csv");

		return response()->download($pathToFile);
	}

	/**
	 * [downloadFailedRows]
	 *
	 * @param  Excel  $excel
	 */
	public function downloadFailedRows(Request $request)
	{
		foreach ($request->input('data') as $row)
			$data[] = unserialize($row);

		return (new FastExcel(collect($data)))->download('failed_rows.xlsx');
	}

	/**
	 * return address array
	 *
	 * @param  array $data
	 * @param  array $type Address Type
	 * @return array $address
	 */
	private function makeAddress($data, $type = 'primary')
	{
		$type = strtolower($type);

		$address = [
			'address_title' => ucfirst($type) . ' Address',
			'address_line_1' => $data[$type.'_address_line_1'],
			'address_line_2' => $data[$type.'_address_line_2'],
			'city' => $data[$type.'_address_city'],
			'zip_code' => $data[$type.'_address_zip_code'],
			'phone' => $data[$type.'_address_phone'],
			'latitude' => Null,
			'longitude' => Null,
		];

		// Get the country id
		if($data[$type.'_address_country_code']){
			$country = DB::table('countries')->select(['id','name'])->where('iso_3166_2', strtoupper($data[$type.'_address_country_code']))->first();
		}
		$address['country_id'] = isset($country) && ! empty($country) ? $country->id : config('system_settings.address_default_country');

		// Get the state id
		if($data[$type.'_address_state_name']){
			$states = ListHelper::states($address['country_id']);
			$state_id = array_search(strtolower($data[$type.'_address_state_name']), array_map('strtolower',$states->toArray()));

			if( ! $state_id )
	            $state_id = State::create(['name' => $data[$type.'_address_state_name'], 'country_name' => $country->name, 'country_id' => $country->id])->id;
		}
		$address['state_id'] = isset($state_id) ? $state_id : config('system_settings.address_default_state');

		return $address;
	}

	/**
	 * Push New value Into Failed List
	 *
	 * @param  array  $data
	 * @param  str $reason
	 * @return void
	 */
	private function pushIntoFailed(array $data, $reason = Null)
	{
		$row = [
			'data' => $data,
			'reason' => $reason,
		];

		array_push($this->failed_list, $row);
	}

	/**
	 * Return the failed list
	 *
	 * @return array
	 */
	private function getFailedList()
	{
		return $this->failed_list;
	}
}
