<?php

namespace App\Common;


use App\Product;
use App\ProductVariant;
use App\AttributeValue;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Validations\CreateProductVariantRequest;


/**
 * Attach this Trait to a User (or other model) for easier read/writes on Addresses
 *
 * @author Munna Khan
 */
trait HasVariant {

    /**
     * Set attribute pivot table for the product variants like color, size and more
     * @param Product $product
     * @param array $attributes
     */
    private function createVariants(Product $product, Request $request)
    {
        $variant_skus = $request->get('variant_skus');
        $variant_prices = $request->get('variant_prices');
        $variant_quantities = $request->get('variant_quantities');
        $mixVariants = $request->input('variants'); // Available When Create

        if(! $variant_skus || ! count($variant_skus) > 0) {
            return;
        }

        foreach ($variant_skus as $indx => $sku) {

            // Skip the variant if any required data is missing
            if( ! $sku || ! isset($variant_prices[$indx]) || ! isset($variant_quantities[$indx]) ) {
                continue;
            }

            $data = [
                'requires_shipping' => $request->get('requires_shipping') ?? $product->requires_shipping,
                'downloadable' => $request->get('downloadable') ?? $product->downloadable,
                'condition' => $request->get('condition') ?? 'New',
                'sku' => $sku,
                'stock_quantity' => $variant_quantities[$indx],
                'price' => $variant_prices[$indx],
                'user_id' => $request->user()->id,
            ];

            // Create the variant
            $variant = $product->variants()->create($data);

            $this->setAttributes($variant, $mixVariants[$indx]);

            if ($request->hasFile('variant_images')) {

                $variant_images = $request->file('variant_images');

                if(isset($variant_images[$indx]) && $variant_images[$indx] != "undefined") {

                    $image = $product->saveImage($variant_images[$indx]); // Save image

                    // Link the vriant to this image
                    $variant->image_id = $image->id;
                    $variant->save();
                }
            }
        }

        return;
    }

    /**
     * Set attribute pivot table for the product variants like color, size and more
     * @param Product $product
     * @param array $attributes
     */
    private function updateVariants(Product $product, Request $request)
    {
        $variant_skus = $request->get('variant_skus');
        $variant_prices = $request->get('variant_prices');
        $variant_quantities = $request->get('variant_quantities');
        $variant_images = $request->file('variant_images');
        $variant_ids = $request->input('variant_ids'); // Available When Update

        foreach ($product->variants as $oldVariant) {

            if ( in_array($oldVariant->id, $variant_ids) ) {
                $data = [
                    'requires_shipping' => $request->get('requires_shipping'),
                    'downloadable' => $request->get('downloadable'),
                    'condition' => $request->get('condition'),
                    'sku' => $variant_skus[$oldVariant->id],
                    'stock_quantity' => $variant_quantities[$oldVariant->id],
                    'price' => $variant_prices[$oldVariant->id],
                    'user_id' => $request->user()->id,
                ];

                if ( $request->hasFile('variant_images') && isset($variant_images[$oldVariant->id])) {
                    $image = $product->saveImage($variant_images[$oldVariant->id]); // Save image
                    $data['image_id'] = $image->id;
                }

                $oldVariant->update($data); // Update the varinats
            }
            else {
                $oldVariant->delete(); // Delete the varinats
            }
        }

        return;
    }

    public function singleVariantForm(Request $request, Product $product)
    {
        $this->authorize('update', $product); // Check permission

        $productAttributeIds = $product->attributes()->pluck('id');

        return view('admin.product.add_variant', compact('product','productAttributeIds'));
    }

    public function saveSingleVariant(CreateProductVariantRequest $request, Product $product)
    {

        $attributes = $request->get('attributes');

        // Verify variant uniqueness
        if( ! $this->verifyVariantUniqueness($product, $attributes) ) {
            return back()->with('error', trans('responses.variant_unique'))->withInput();
        }

        // Create the variant
        $data = [
            'title' => $product->title == $request->get('title') ? $request->get('title') : Null,
            'available_from' => $product->available_from == $request->get('available_from') ? $request->get('available_from') : Null,
            'requires_shipping' => $request->get('requires_shipping'),
            'downloadable' => $request->get('downloadable'),
            'condition' => $request->get('condition'),
            'sku' => $request->get('sku'),
            'stock_quantity' => $request->get('stock_quantity'),
            'min_order_quantity' => $request->get('min_order_quantity'),
            'shipping_weight' => $request->get('shipping_weight'),
            'user_id' => $request->get('user_id'),
            'price' => $request->get('price'),
            'offer_price' => $request->get('offer_price'),
        ];

        $variant = $product->variants()->create($data);

        $this->setAttributes($variant, $attributes);

        if ($request->hasFile('image')) {
            $image = $product->saveImage($request->file('image')); // Save image
            // Link the vriant to this image
            $variant->image_id = $image->id;
            $variant->save();
        }

        $request->session()->flash('success', trans('messages.created', ['model' => trans('app.variant')]));

        return redirect()->route('admin.catalog.product.edit', $product);
    }


    /**
     * Set attribute pivot table for the product variants like color, size and more
     * @param Product $product
     * @param array $attributes
     */
    private function setAttributes(ProductVariant $product, $attributes)
    {
        $attributes = array_filter($attributes ?? []);// remove empty elements

        $temp = [];
        foreach ($attributes as $attribute_id => $attribute_value_id){
            $temp[$attribute_id] = ['attribute_value_id' => $attribute_value_id];
        }

        if (!empty($temp)){
            $product->attributes()->sync($temp);
        }

        return true;
    }

    /**
     * Check the list of attribute values and add new if need
     *
     * @param  [type] $attribute
     * @return array
     */
    public function confirmAttributes($attributeWithValues)
    {
        $results = array();

        foreach ($attributeWithValues as $attribute => $values){
            foreach ($values as $value){
                $oldValueId = AttributeValue::find($value);

                $oldValueName = AttributeValue::where('value', $value)->where('attribute_id', $attribute)->first();

                if ($oldValueId || $oldValueName){
                    $results[$attribute][($oldValueId) ? $oldValueId->id : $oldValueName->id] = ($oldValueId) ? $oldValueId->value : $oldValueName->value;
                }
                else{
                    // if the value not numeric thats meaninig that its new value and we need to create it
                    $newID = AttributeValue::insertGetId(['attribute_id' => $attribute, 'value' => $value]);

                    $newAttrValue = AttributeValue::find($newID);

                    $results[$attribute][$newAttrValue->id] = $newAttrValue->value;
                }
            }
        }

        return $results;
    }

    /**
     * Verify variant uniqueness
     *
     * @param  Product $product
     * @param  array $attributes
     *
     * @return bool
     */
    public function verifyVariantUniqueness(Product $product, $attributes = [])
    {
        $product->load(['variants' => function($q){
            $q->with('attributes:id');
        }]) ;

        foreach ($product->variants->pluck('attributes') as $value) {
            $tempAttrs = $value->pluck('pivot.attribute_value_id','id')->toArray();

            if( $tempAttrs == $attributes ) {
                return FALSE;
            }
        }

        return TRUE;
    }

}
