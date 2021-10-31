<?php

namespace App;

use Auth;
use Carbon\Carbon;
use App\Common\Imageable;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use Imageable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_variants';

    /**
     * The attributes that should be casted to boolean types.
     *
     * @var array
     */
    protected $casts = [
        'requires_shipping' => 'boolean',
        'downloadable' => 'boolean',
        'free_shipping' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['offer_start', 'offer_end', 'available_from'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                        'product_id',
                        'title',
                        'model_number',
                        'mpn',
                        'gtin',
                        'gtin_type',
                        'description',
                        'requires_shipping',
                        'downloadable',
                        'image_id',
                        'warehouse_id',
                        'sku',
                        'condition',
                        'condition_note',
                        'key_features',
                        'stock_quantity',
                        'damaged_quantity',
                        'user_id',
                        'purchase_price',
                        'price',
                        'offer_price',
                        'offer_start',
                        'offer_end',
                        'shipping_weight',
                        'free_shipping',
                        'available_from',
                        'min_order_quantity',
                        'sale_count',
                        'active'
                    ];

    /**
     * Get the image associated with the variant.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * Get the product associated with the variant.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the Attributes for the inventory.
     */
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_product', 'product_id', 'attribute_id')
        ->withPivot('attribute_value_id')->withTimestamps();
    }

    /**
     * Get the attribute values that owns the SubGroup.
     */
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_product', 'product_id', 'attribute_value_id')
        ->withPivot('attribute_id')->withTimestamps();
    }

    /**
     * Get the carts for the product.
     */
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items', 'variant_id')
        ->withPivot('id', 'product_id', 'variant_id', 'item_description', 'quantity', 'unit_price')->withTimestamps();
    }

    /**
     * Get the orders for the product.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
        ->withPivot('id', 'item_description', 'variant_id', 'quantity', 'unit_price', 'feedback_id')->withTimestamps();
    }

    /**
     * Check if the stock is low.
     */
    public function isLowQtt()
    {
        $alert_quantity = config('shop_settings.alert_quantity') ?: 0;

        return $this->stock_quantity <= $alert_quantity;
    }

    /**
     * Check if the item hase a valid offer price.
     */
    public function hasOffer()
    {
        if(
            ($this->offer_price > 0) &&
            ($this->offer_price < $this->price) &&
            ($this->offer_start < Carbon::now()) &&
            ($this->offer_end > Carbon::now())
        )
            return TRUE;

        return FALSE;
    }

    /**
     * Return currnt sale price
     *
     * @return number
     */
    public function current_price()
    {
        return $this->hasOffer() ? $this->offer_price : $this->price;
    }

    /**
     * Setters
     */
    public function setOfferPriceAttribute($value)
    {
        if ($value > 0) $this->attributes['offer_price'] = $value;
        else $this->attributes['offer_price'] = null;
    }
   /* public function setOfferStartAttribute($value)
    {
        if($value) $this->attributes['offer_start'] = Carbon::createFromFormat('Y-m-d h:i a', $value);
    }
    public function setOfferEndAttribute($value)
    {
        if($value) $this->attributes['offer_end'] = Carbon::createFromFormat('Y-m-d h:i a', $value);
    }*/
    public function setFreeShippingAttribute($value)
    {
        $this->attributes['free_shipping'] = (bool) $value;
    }
    // public function setKeyFeaturesAttribute($value)
    // {
    //     if(is_array($value))
    //         $value = array_filter($value, function($item) { return !empty($item[0]); });

    //     $this->attributes['key_features'] = serialize($value);
    // }

    /**
     * Set the requires_shipping for the Product.
     */
    public function setRequiresShippingAttribute($value)
    {
        $this->attributes['requires_shipping'] = (bool) $value;
    }

    /**
     * Set the downloadable for the Product.
     */
    public function setDownloadableAttribute($value)
    {
        $this->attributes['downloadable'] = (bool) $value;
    }

    /**
     * Scope a query to only include available for sale .
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where([
            ['active', '=', 1],
            ['stock_quantity', '>', 0],
            ['available_from', '<=', Carbon::now()]
        ]);
    }

    /**
     * Scope a query to only include available for sale .
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasOffer($query)
    {
        return $query->where([
            ['offer_price', '>', 0],
            ['offer_start', '<', Carbon::now()],
            ['offer_end', '>', Carbon::now()]
        ])->whereColumn('offer_price', '<', 'price');
    }

    /**
     * Scope a query to only include items with free Shipping.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFreeShipping($query)
    {
        return $query->where('free_shipping', 1);
    }

    /**
     * Scope a query to only include new Arraival Items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNewArraivals($query)
    {
        return $query->where('inventories.created_at', '>', Carbon::now()->subDays(config('system.filter.new_arraival', 7)));
    }

    /**
     * Scope a query to only include low qtt items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLowQtt($query)
    {
        $alert_quantity = config('shop_settings.alert_quantity') ?: 0;

        return $query->where('stock_quantity', '<=', $alert_quantity);
    }

    /**
     * Scope a query to only include out of stock items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStockOut($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    public function setKeyFeaturesAttribute($value)
    {
        if( is_array($value) ) {
            $value = array_filter($value, function($item) { return !empty($item[0]); });
        }

        $this->attributes['key_features'] = serialize($value);
    }

    /**
     * Scope a query to only include active products.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    /**
     * Returns translated name of condition
     *
     * @return str condition
     */
    public function getConditionAttribute($value)
    {
         if($this->downloadable) {
            return trans('app.downloadable');
        }

        switch ($value) {
            case 'New': return trans('app.new');
            case 'Used': return trans('app.used');
            case 'Refurbished': return trans('app.refurbished');
        }
    }

}
