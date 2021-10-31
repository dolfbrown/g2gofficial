<?php

namespace App;

use Auth;
use Carbon\Carbon;
// use App\Common\Salable;
use App\Common\Taggable;
use App\Common\Imageable;
use App\Common\Attachable;
use App\Common\Feedbackable;
use Laravel\Scout\Searchable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use SoftDeletes, Taggable, Imageable, Attachable, Searchable, Filterable, Feedbackable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that should be casted to boolean types.
     *
     * @var array
     */
    protected $casts = [
        'requires_shipping' => 'boolean',
        'downloadable' => 'boolean',
        'free_shipping' => 'boolean',
        'stuff_pick' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'offer_start', 'offer_end', 'available_from'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                        'manufacturer_id',
                        'brand',
                        'title',
                        'model_number',
                        'mpn',
                        'gtin',
                        'gtin_type',
                        'description',
                        'origin_country',
                        'requires_shipping',
                        'downloadable',
                        'sale_count',
                        'warehouse_id',
                        'brand',
                        'supplier_id',
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
                        'linked_items',
                        'slug',
                        'meta_title',
                        'meta_description',
                        'stuff_pick',
                        'active'
                    ];

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey()
    {
        return $this->name;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        $array['id'] = $this->id;
        $array['manufacturer_id'] = $this->manufacturer_id;
        $array['title'] = $this->title;
        $array['model_number'] = $this->model_number;
        $array['mpn'] = $this->mpn;
        $array['gtin'] = $this->gtin;
        $array['description'] = $this->description;
        $array['key_features'] = $this->key_features;
        $array['active'] = $this->active;

        return $array;
    }

    /**
     * Get the user variants of the warehouses.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Overwrited the image method in the imageable
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable')
        ->where(function($q){
            $q->whereNull('featured')->orWhere('featured', 0);
        })->orderBy('order', 'asc');
    }

    /**
     * Get the origin associated with the product.
     */
    public function origin()
    {
        return $this->belongsTo(Country::class, 'origin_country');
    }

    /**
     * Get the manufacturer associated with the product.
     */
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class)->withDefault();
    }

    /**
     * Get the attributes for the product via variant.
     */
    public function attributes()
    {
        $variant = $this->variants->first();

        return $variant ? $variant->attributes() : collect([]);
    }

    /**
     * Get the carts for the product.
     */
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items')
        ->withPivot('id', 'item_description', 'variant_id', 'quantity', 'unit_price')->withTimestamps();
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
     * Get the categories for the product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
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
        ) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Return currnt sale price
     *
     * @return number
     */
    public function currnt_price()
    {
        if($this->hasOffer()) {
            return $this->offer_price;
        }

        return $this->price;
    }

    /**
     * Set the requires_shipping for the Product.
     */
    public function hasVariants()
    {
        return (bool) $this->variants()->count();
    }

    /**
     * Get the category list for the product.
     *
     * @return array
     */
    public function getCategoryListAttribute()
    {
        if (count($this->categories)) {
            return $this->categories->pluck('id')->toArray();
        }
    }

    /**
     * Setters
     */
    public function setMinOrderQuantityAttribute($value)
    {
        if ($value > 1) {
            $this->attributes['min_order_quantity'] = $value;
        }
        else {
            $this->attributes['min_order_quantity'] = 1;
        }
    }
    public function setOfferPriceAttribute($value)
    {
        if ($value > 0) {
            $this->attributes['offer_price'] = $value;
        }
        else {
            $this->attributes['offer_price'] = null;
        }
    }
    public function setAvailableFromAttribute($value)
    {
        if($value) {
            $this->attributes['available_from'] = Carbon::createFromFormat('Y-m-d h:i a', $value);
        }
    }
    public function setOfferStartAttribute($value)
    {
        if($value) {
            $this->attributes['offer_start'] = Carbon::createFromFormat('Y-m-d h:i a', $value);
        }
    }
    public function setOfferEndAttribute($value)
    {
        if($value) {
            $this->attributes['offer_end'] = Carbon::createFromFormat('Y-m-d h:i a', $value);
        }
    }
    public function setKeyFeaturesAttribute($value)
    {
        if( is_array($value) ) {
            $value = array_filter($value, function($item) { return !empty($item[0]); });
        }

        $this->attributes['key_features'] = serialize($value);
    }
    public function setLinkedItemsAttribute($value)
    {
        $this->attributes['linked_items'] = serialize($value);
    }

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
     * Get the Warehouse associated with the inventory.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the supplier for the inventory.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withDefault();
    }

    /**
     * Get the packagings for the product.
     */
    public function packagings()
    {
        return $this->belongsToMany(Packaging::class)->withTimestamps();
    }

    public function isLowQtt()
    {
        $alert_quantity = config('shop_settings.alert_quantity') ?: 0;

        return $this->stock_quantity <= $alert_quantity;
    }

    /**
     * Setters
     */
    public function setWarehouseIdAttribute($value)
    {
        if ($value > 0) {
            $this->attributes['warehouse_id'] = $value;
        }
        else {
            $this->attributes['warehouse_id'] = null;
        }
    }
    public function setSupplierIdAttribute($value)
    {
        if ($value > 0) {
            $this->attributes['supplier_id'] = $value;
        }
        else {
            $this->attributes['supplier_id'] = null;
        }
    }
    public function setFreeShippingAttribute($value)
    {
        $this->attributes['free_shipping'] = (bool) $value;
    }

    /**
     * Getters
     */
    public function getPackagingListAttribute()
    {
        if (count($this->packagings)) {
            return $this->packagings->pluck('id')->toArray();
        }
    }

    /**
     * Scope a query to only include downloadable products
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePhysical($query)
    {
        return $query->whereNull('downloadable');
    }

    /**
     * Scope a query to only include downloadable products
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDownloadable($query)
    {
        return $query->whereNotNull('downloadable');
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
     * Scope a query to only include items with free Shipping.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFreeShipping($query)
    {
        return $query->where('free_shipping', 1);
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
     * Scope a query to only include new Arraival Items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNewArraivals($query)
    {
        return $query->where('inventories.created_at', '>', Carbon::now()
        ->subDays(config('system.filter.new_arraival', 7)));
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