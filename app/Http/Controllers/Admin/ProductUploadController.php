<?php

namespace App\Http\Controllers\Admin;

use App\Common\HasVariant;
use App\Packaging;
use App\ProductVariant;
use DB;
use App\Product;
use App\Category;
use App\Manufacturer;
use App\Attribute;
use App\AttributeValue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Http\Requests\Validations\ExportCategoryRequest;
use App\Http\Requests\Validations\ProductUploadRequest;
use App\Http\Requests\Validations\ProductImportRequest;

class ProductUploadController extends Controller
{
    use HasVariant;

	private $failed_list = [];

	/**
	 * Show upload form
	 *
     * @return \Illuminate\Http\Response
	 */
	public function showForm()
	{
        return view('admin.product._upload_form');
	}

	/**
	 * Upload the csv file and generate the review table
	 *
	 * @param  ProductUploadRequest $request
     * @return \Illuminate\Http\Response
	 */
	public function upload(ProductUploadRequest $request)
	{
		$path = $request->file('products')->getRealPath();
		$records = array_map('str_getcsv', file($path));

	    // Validations check for csv_import_limit
	    if((count($records) - 1) > get_csv_import_limit()){
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

        return view('admin.product.upload_review', compact('rows'));
	}

	/**
	 * Perform import action
	 *
	 * @param  ProductImportRequest $request
     * @return \Illuminate\Http\Response
	 */
	public function import(ProductImportRequest $request)
	{
		// Reset the Failed list
		$this->failed_list = [];

		foreach ($request->input('data') as $row) {
			$data = unserialize($row);

			// Ignore if the title field is not given
			if(! $data['title'] || ! $data['categories']){
				$reason = $data['title'] ? trans('help.invalid_category') : trans('help.title_field_required');
				$this->pushIntoFailed($data, $reason);
				continue;
			}

			// If the slug is not given the make it
			if(! $data['slug']) {
				$data['slug'] = convertToSlugString($row['title'], $row['sku']);
			}

			// Ignore if the slug is exist in the database
			/*$product = Product::select('slug')->where('slug', $data['slug'])->first();
			if($product){
				$this->pushIntoFailed($data, trans('help.slug_already_exist'));
				continue;
			}*/

			// Find categories and make the category_list. Ignore the row if category not found
			$data['category_list'] = Category::whereIn('slug', explode(',', $data['categories']))->pluck('id')->toArray();

			if(empty($data['category_list'])){
				$this->pushIntoFailed($data, trans('help.invalid_category'));
				continue;
			}

			// Create the product and get it, If failed then insert into the ignored list
			if(! $this->createProduct($data)){

				$this->pushIntoFailed($data, trans('help.input_error'));
				continue;
			}
		}

        $request->session()->flash('success', trans('messages.imported', ['model' => trans('app.products')]));

        $failed_rows = $this->getFailedList();

		if(! empty($failed_rows)) {
	        return view('admin.product.import_failed', compact('failed_rows'));
		}

        return redirect()->route('admin.catalog.product.index');
	}

	/**
	 * Create Product
	 *
	 * @param  array $product
	 * @return App\Product
	 */
	private function createProduct($data)
	{

		if($data['origin_country']) {
			$origin_country = DB::table('countries')->select('id')->where('iso_3166_2', strtoupper($data['origin_country']))->first();
		}

		if($data['manufacturer']) {
			$manufacturer = Manufacturer::firstOrCreate(['name' => $data['manufacturer']]);
		}

        $key_features = array_filter($data, function($key) {
            return strpos($key, 'key_feature_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        if ($data['linked_items']) {
            $temp_arr = explode(',', $data['linked_items']);
            $linked_items = Product::select('id')->whereIn('sku', $temp_arr)->pluck('id')->toArray();
        }

        $product = Product::where('slug', $data['slug'])->first();
        if (! $product) {
            // Create the product
            $product = Product::create([
                'user_id' => request()->user()->id,
                'title' => $data['title'],
                'slug' => $data['slug'],
                'sku' => $data['sku'],
                'condition' => ucfirst($data['condition']),
                'condition_note' => $data['condition_note'],
                'model_number' => $data['model_number'],
                'description' => $data['description'],
                'gtin' => $data['gtin'],
                'gtin_type' => $data['gtin_type'],
                'mpn' => $data['mpn'],
                'brand' => $data['brand'],
                'stock_quantity' => $data['stock_quantity'],
                'min_order_quantity' => $data['min_order_quantity'],
                'key_features' => $key_features,
                'origin_country' => isset($origin_country) ? $origin_country->id : Null,
                'manufacturer_id' => isset($manufacturer) ? $manufacturer->id : Null,
                'warehouse_id' => $data['warehouse_id'],
                'supplier_id' => $data['supplier_id'],
                'price' => $data['price'],
                'offer_price' => $data['offer_price'],
                'offer_start' => (!empty($data['offer_starts']) ? date('Y-m-d h:i a', strtotime($data['offer_starts'])) : Null),
                'offer_end' => (!empty($data['offer_ends']) ? date('Y-m-d h:i a', strtotime($data['offer_ends'])) : Null),
                'purchase_price' => empty($data['purchase_price']) ? Null : $data['purchase_price'],
                'linked_items' => isset($linked_items) ? $linked_items : Null,
                'meta_title' => $data['meta_title'],
                'meta_description' => $data['meta_description'],
                //'has_variant' => strtoupper($data['has_variant']) == 'TRUE' ? 1 : 0,
                'requires_shipping' => strtoupper($data['requires_shipping']) == 'TRUE' ? 1 : 0,
                'shipping_weight' => $data['shipping_weight'],
                'available_from' => (!empty($data['available_from']) ? date('Y-m-d h:i a', strtotime($data['available_from'])) : date('Y-m-d h:i a')),
                'active' => strtoupper($data['active']) == 'TRUE' ? 1 : 0,
            ]);

            // Sync categories
            if($data['category_list']) {
                $product->categories()->sync($data['category_list']);
            }


            // Upload images
            if($data['image_links']){
                $image_links = explode(',', $data['image_links']);

                foreach ($image_links as $image_link) {
                    if (filter_var($image_link, FILTER_VALIDATE_URL)){
                        $product->saveImageFromUrl($image_link);
                    }
                }
            }

            // Sync packaging
            if($data['packaging_ids']){
                $temp_arr = explode(',', $data['packaging_ids']);
                $packaging_ids = Packaging::select('id')->mine()->whereIn('id', $temp_arr)->pluck('id')->toArray();

                $product->packaging()->sync($packaging_ids);
            }

            // Sync tags
            if($data['tags']) {
                $product->syncTags($product, explode(',', $data['tags']));
            }

        }

        //Create Variants:
        self::createVariant($product, $data, $key_features);

		return $product;
	}

	/**
	 * [downloadCategorySlugs]
	 *
	 * @param  Excel  $excel
	 */
	public function downloadCategorySlugs(ExportCategoryRequest $request)
	{
		$categories = Category::select('name','slug')->get();

		return (new FastExcel($categories))->download('categories.xlsx');
	}

	/**
	 * downloadTemplate
	 *
	 * @return response response
	 */
	public function downloadTemplate()
	{
		$pathToFile = public_path("csv_templates/products.csv");

		return response()->download($pathToFile);
	}


	/**
	 * [downloadFailedRows]
	 *
	 * @param  Excel  $excel
	 */
	public function downloadFailedRows(Request $request)
	{
		foreach ($request->input('data') as $row) {
			$data[] = unserialize($row);
		}

		return (new FastExcel(collect($data)))->download('failed_rows.xlsx');
	}

    /**
     * Set attribute pivot table for the product variants like color, size and more
     * @param obj $inventory
     * @param array $attributes
     */
    // public function setAttributes($inventory, $attributes)
    // {
    //     $attributes = array_filter($attributes ?? []);        // remove empty elements

    //     $temp = [];
    //     foreach ($attributes as $attribute_id => $attribute_value_id){
    //         $temp[$attribute_id] = ['attribute_value_id' => $attribute_value_id];
    //     }

    //     if (!empty($temp)){
    //         $inventory->attributes()->sync($temp);
    //     }

    //     return true;
    // }

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

    public function createVariant($product, $data, $key_features){

        $productVariant = ProductVariant::create([
            'user_id' => request()->user()->id,
            'product_id' => $product->id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'sku' => $data['sku'],
            'condition' => ucfirst($data['condition']),
            'condition_note' => $data['condition_note'],
            'model_number' => $data['model_number'],
            'description' => $data['description'],
            'gtin' => $data['gtin'],
            'gtin_type' => $data['gtin_type'],
            'mpn' => $data['mpn'],
            'brand' => $data['brand'],
            'stock_quantity' => $data['stock_quantity'],
            'min_order_quantity' => $data['min_order_quantity'],
            'key_features' => $key_features,
            'origin_country' => $product->origin_country,
            'manufacturer_id' => $product->manufacturer_id,
            'warehouse_id' => $data['warehouse_id'],
            'supplier_id' => $data['supplier_id'],
            'price' => $data['price'],
            'offer_price' => $data['offer_price'],
            'offer_start' => $product->offer_start,
            'offer_end' => $product->offerend,
            'purchase_price' => empty($data['purchase_price']) ? Null : $data['purchase_price'],
            'linked_items' => $product->linked_items,
            'meta_title' => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'requires_shipping' => strtoupper($data['requires_shipping']) == 'TRUE' ? 1 : 0,
            'shipping_weight' => $data['shipping_weight'],
            'available_from' => $product->available_from,
            'active' => strtoupper($data['active']) == 'TRUE' ? 1 : 0,
        ]);

        // Set attributes
        $attributes = [];
        $variants = array_filter($data, function($key) {
            return strpos($key, 'option_name_') === 0;

        }, ARRAY_FILTER_USE_KEY);

        foreach($variants as $index => $option){
            $count = explode('_', $index);
            if($data[$index] && $data['option_value_'.$count[2]]){
                $att = Attribute::select('id')->where('name', $option)->first();
                $val = AttributeValue::firstOrCreate([
                    'value' => $data['option_value_'.$count[2]],
                    'attribute_id' => $att->id
                ]);

                if($att && $val) {
                    $attributes[$att->id] = $val->id;
                }
            }
        }

        if (! empty($attributes)) {
            $this->setAttributes($productVariant, $attributes); // Sync the attributes with the product
        }

        return $productVariant;

	}



}
