<?php

namespace App\Helpers;

use Auth;
use Carbon\Carbon;
use App\User;
use App\Role;
use App\Page;
use App\Blog;
use App\Order;
use App\Module;
use App\Refund;
use App\Visitor;
use App\Message;
use App\Product;
use App\Dispute;
use App\Customer;
use App\FaqTopic;
use App\Category;
use App\Supplier;
use App\Attribute;
use App\Permission;
use App\Announcement;
use App\PaymentMethod;
use App\CategoryGroup;
use App\CategorySubGroup;

/**
* This is a helper class to process,upload and remove images from different models
*/

class ListHelper
{
    /**
     * Get payment_method_types list for form dropdown.
     *
     * @return array
     */
    public static function payment_method_types()
    {
        return  [
            PaymentMethod::TYPE_PAYPAL      => trans("app.payment_method_type.paypal.name"),
            PaymentMethod::TYPE_CREDIT_CARD => trans("app.payment_method_type.credit_card.name"),
            PaymentMethod::TYPE_MANUAL      => trans("app.payment_method_type.manual.name"),
            PaymentMethod::TYPE_OTHERS      => trans("app.payment_method_type.others.name"),
        ];
    }
    public static function payment_statuses()
    {
        return  [
            Order::PAYMENT_STATUS_UNPAID    => trans("app.statuses.unpaid"),
            Order::PAYMENT_STATUS_PENDING   => trans("app.statuses.pending"),
            Order::PAYMENT_STATUS_PAID      => trans("app.statuses.paid"),
        ];
    }

    /**
     * Get dispute statuses list for form dropdown.
     *
     * @return array
     */
    public static function dispute_statuses()
    {
        $statuses =  [
            Dispute::STATUS_NEW      => trans("app.statuses.new"),
            Dispute::STATUS_OPEN     => trans("app.statuses.open"),
            Dispute::STATUS_WAITING  => trans("app.statuses.waiting"),
            Dispute::STATUS_SOLVED   => trans("app.statuses.solved"),
            Dispute::STATUS_CLOSED   => trans("app.statuses.closed"),
        ];

        if( ! Auth::user() instanceOf Customer )
            $statuses[Dispute::STATUS_APPEALED] = trans("app.statuses.appealed");

        return $statuses;
    }

    /**
     * Get refund statuses list for form dropdown.
     *
     * @return array
     */
    public static function refund_statuses()
    {
        return  [
            Refund::STATUS_NEW      => trans("app.statuses.new"),
            Refund::STATUS_APPROVED  => trans("app.statuses.approved"),
            Refund::STATUS_DECLINED => trans("app.statuses.declined"),
        ];
    }

    /**
     * Get page positions list for form dropdown.
     *
     * @return array
     */
    public static function page_positions()
    {
        return  [
            'copyright_area'    => trans("app.copyright_area"),
            'footer_1st_column' => trans("app.footer_1st_column"),
            'footer_2nd_column' => trans("app.footer_2nd_column"),
            'footer_3rd_column' => trans("app.footer_3rd_column"),
            'main_nav'           => trans("app.main_nav"),
        ];
    }

    /**
     * Get system ettings.
     *
     * @return array
     */
    public static function system_settings()
    {
        return (array) \DB::table('systems')->orderBy('id', 'asc')->first();
    }

    /**
     * Get active announcement.
     *
     * @return array
     */
    public static function activeAnnouncement()
    {
        return Announcement::orderBy('created_at', 'desc')->first();
    }

    /**
     * Get role list for form dropdown.
     * otherwise return roles thats not public
     *
     * @return array
     */
    public static function roles()
    {
        $roles = Role::lowerPrivileged();

        return $roles->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get list of all available categories formated for theme
     *
     * @return array
     */
    public static function categoriesForTheme()
    {
        return CategoryGroup::select('id','name','slug','icon')
        ->with(['image:path,imageable_id,imageable_type', 'subGroups' => function($query){
            $query->select('id','slug','category_group_id','name')
                ->active()->has('categories.products')
                ->withCount('categories')->orderBy('categories_count', 'desc');
        },
        'subGroups.categories' => function($query){
            $query->select('id','category_sub_group_id','name','slug','description')
            ->has('products')->active();
        }])
        ->has('subGroups.categories.products')->active()->orderBy('order', 'asc')->get();
    }

    /**
     * Get list of all available category group
     *
     * @return array
     */
    public static function categoryGrps()
    {
        return \DB::table('category_groups')->where('deleted_at', Null)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get list of category sub-group
     *
     * @return array
     */
    public static function catSubGrps()
    {
        return CategorySubGroup::orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get list of category sub-group under the given category
     *
     * @return array
     */
    public static function thisCatSubGrps($category)
    {
        return \DB::table('category_sub_groups')->where('deleted_at', Null)
        ->where('category_group_id', $category)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get categories list for form dropdown.
     *
     * @return array
     */
    public static function categories()
    {
        return Category::orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get search_categories list for form dropdown.
     *
     * @return array
     */
    public static function search_categories()
    {
        return Category::orderBy('name', 'asc')->pluck('name', 'slug');
    }

    /**
     * Get all catGrpSubGrpListArray
     *
     * @return array
     */
    public static function catGrpSubGrpListArray()
    {
        $grps = [];
        foreach (self::categoryGrps() as $key => $value){
            $list = [];

            foreach (self::thisCatSubGrps($key) as $key2 => $value2){
                $list[$key2] = $value2;
            }

            if(count($list)){
                $grps[$value] = $list;
            }
        }

        return $grps;
    }

    /**
     * Get all catWithSubGrpList
     *
     * @return array
     */
    public static function catWithSubGrpListArray()
    {
        $categoryGroups = CategoryGroup::select(['id','name'])->active()->orderBy('name', 'asc')
        ->with(['subGroups' => function($q){
            $q->select(['id','name','category_group_id'])->orderBy('name', 'asc')->active();
        },'subGroups.categories' => function($q){
            $q->select(['id','category_sub_group_id','name'])->active();
        }])->get();

        $grps = [];
        foreach ($categoryGroups as $categoryGroup){
            foreach ($categoryGroup->subGroups as $categorySubGroup){
                $list = [];

                foreach ($categorySubGroup->categories as $category)
                    $list[$category->id] = $category->name;

                if(count($list))
                    $grps[$categoryGroup->name.': '.$categorySubGroup->name] = $list;
            }
        }

        return $grps;
    }

    /**
     * Get permissions list for form dropdown.
     *
     * @return array
     */
    public static function permissions()
    {
        return Permission::orderBy('module_id', 'asc')->pluck('name', 'id');
    }

    /**
     * Get modulesWithPermissions list.
     *
     * @return array
     */
    public static function modulesWithPermissions()
    {
        return Module::active()->with('permissions')->orderBy('name', 'asc')->get();
    }

    /**
     * Get array of slugsWithModulAccess list.
     *
     * @return array
     */
    public static function slugsWithModulAccess()
    {
        return Permission::with('module')->get()->pluck('module.access', 'slug')->toArray();
    }

    /**
     * Get Popular_blogs list.
     *
     * @return array
     */
    public static function popularBlogs()
    {
        return Blog::select(['id','title','slug','excerpt','published_at'])->popular()->take(5)->get();
    }

    /**
     * Get latest_blogs list.
     *
     * @return array
     */
    public static function recentBlogs()
    {
        return Blog::select(['id','title','slug','excerpt','published_at'])->recent()->take(5)->get();
    }

    /**
     * Get users list for form dropdown.
     *
     * @return array
     */
    public static function users()
    {
        return \DB::table('users')->where('deleted_at', Null)->where('active', 1)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get users list for form dropdown.
     *
     * @return array
     */
    public static function platform_users()
    {
        return \DB::table('users')->where('role_id', '!=', 3)
        ->where('deleted_at', Null)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    public static function shipping_zones()
    {
        return \DB::table('shipping_zones')->where('active', 1)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get permission slugs for the user role.
     *
     * @return array
     */
    public static function authorizations(User $user = null)
    {
        $user = $user ?: Auth::guard('web')->user(); //Get current user

        if($user->isSuperAdmin()){
            return [];
        }

        return $user->role->permissions()->pluck('slug')->toArray();
    }

    /**
     * Get all FAQ topic list for form dropdown.
     *
     * @return array
     */
    public static function faq_topics()
    {
        return \DB::table('faq_topics')->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * [top_customers description]
     *
     * @return [type] [description]
     */
    public static function top_customers($limit = 5)
    {
        incevioAutoloadHelpers(getMysqliConnection());
        return Customer::select('id','nice_name','name','email')
        ->with('image:path,imageable_id,imageable_type')
        ->whereHas('orders', function($query){
            $query->select('customer_id','total')->withArchived();
        })->withCount(['orders' => function($q){
            $q->withArchived();
        }])->orderBy('orders_count', 'desc')->limit($limit)->get();
    }

    /**
     * [returning_customers description]
     *
     * @return [type] [description]
     */
    public static function returning_customers($limit = 5)
    {
        $customers = static::top_customers($limit);

        // Return customer has more than one orders
        return $customers->filter(function ($customer, $key) {
            return $customer->orders->count() > 1;
        });
    }

    /**
     * Return unique brand names from the given linstings
     *
     * @return array
     */
    public static function get_unique_brand_names_from_linstings($listings)
    {
        return $listings->pluck('brand')->unique();
    }

    /**
     * Return minimum and maximum price from the given linstings
     *
     * @return array
     */
    public static function get_price_ranges_from_linstings($listings)
    {
        $priceRange = [];

        $priceRange['min'] = floor($listings->min('price'));
        $priceRange['max'] = ceil($listings->max('price'));

        return $priceRange;
    }

    /**
     * Get all merchants list for form dropdown.
     *
     * @return array
     */
    public static function merchants()
    {
        return \DB::table('users')->where('role_id', 3)->where('deleted_at', Null)
        ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get suppliers list for form dropdown.
     *
     * @return array
     */
    public static function suppliers()
    {
        return \DB::table('suppliers')->where('deleted_at', Null)
        ->where('active', 1)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get timezones list for form dropdown.
     *
     * @return array
     */
    public static function timezones()
    {
        return \DB::table('timezones')->pluck('text', 'id');
    }

    /**
     * Get warehouses list for form dropdown.
     *
     * @return array
     */
    public static function warehouses()
    {
        return \DB::table('warehouses')->where('deleted_at', Null)
        ->where('active', 1)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get carriers list for form dropdown.
     *
     * @return array
     */
    public static function carriers()
    {
        return \DB::table('carriers')
        ->where('deleted_at', Null)->where('active', 1)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get taxes list for form dropdown.
     *
     * @return array
     */
    public static function taxes()
    {
        return \DB::table('taxes')->where('active', 1)->where('deleted_at', Null)
        ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get customers list for form dropdown.
     * @return array
     */
    public static function customers()
    {
        return \DB::table('customers')->where('deleted_at', Null)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get inventories list for form dropdown.
     * @return array
     */
    public static function products($type = 'physical')
    {
        $list = \DB::table('products')->where('deleted_at', Null);

        if($type == 'downloadable') {
            $list->whereNotNull('downloadable');
        }
        else {
            $list->whereNull('downloadable');
        }

        return $list->orderBy('title', 'asc')->pluck('title', 'id');
    }

    /**
     * Get top listing_items list for merchnat.
     * @return array
     */
    public static function top_listing_items($count = 5)
    {
        return Product::with('image:path,imageable_id,imageable_type')
        ->select(
            'products.id','products.sku','products.title',
            \DB::raw('SUM(order_items.quantity) as sold_qtt'),
            \DB::raw('SUM(order_items.unit_price) as gross_sales')
        )
        ->join('order_items', 'products.id', 'order_items.product_id')
        ->groupBy('products.id')->orderBy('sold_qtt', 'desc')->limit($count)->get();
    }

    /**
     * Get top categories list for merchnat.
     * @return array
     */
    public static function top_categories($count = 5)
    {
        return Category::select('id','slug','name','active')->whereHas('products')
        ->withCount('products')->orderBy('products_count', 'desc')->limit($count)->get();
    }

    /**
     * Get top suppliers list for merchnat.
     * @return array
     */
    public static function top_suppliers($count = 5)
    {
        return Supplier::select('id','name','active')->with('image:path,imageable_id,imageable_type')->withCount('products')
        ->orderBy('products_count', 'desc')->limit($count)->get();
    }

    /**
     * Get trendiing items list. Get the most ordered item in given days
     * @return array
     */
    public static function popular_items($days = 7, $count = 15)
    {
        return Product::available()->withCount(['orders' => function($q){
            $q->withArchived();
        }])->orderBy('orders_count', 'desc')
        ->with([
            'feedbacks:rating,feedbackable_id,feedbackable_type',
            'featuredImage:path,imageable_id,imageable_type',
        ])
        ->limit($count)->get();
    }

    /**
     * Get latest_products list
     *
     * @return array
     */
    public static function latest_products()
    {
        return Product::with('featuredImage')->latest()->limit(10)->get();
    }

    /**
     * Get latest products that has live listing
     * @return array
     */
    public static function latest_available_items($limit = 10)
    {
        return Product::with([
            'feedbacks:rating,feedbackable_id,feedbackable_type',
            'featuredImage:path,imageable_id,imageable_type',
        ])
        ->available()->latest()->limit($limit)->get();
    }

    /**
     * Get variants of product of given item
     * @return array
     */
    // public static function variants_of_product($item)
    // {
    //     $variants = Product::select('id','slug','title','stock_quantity','condition','price','offer_price','offer_start','offer_end','min_order_quantity')->where('id', $item)->available();

    //     return $variants->with([
    //         'images:path,imageable_id,imageable_type',
    //         'attributeValues:id,value,color'
    //     ])->get();
    //     // return $variants->with('attributeValues:id,value,color')->available()->get();
    // }

    /**
     * Get related products of given item
     * @return array
     */
    public static function related_products($item, $limit = 10)
    {
        $catIds = $item->categories->pluck('id');

        $productIDs = \DB::table('category_product')->whereIn('category_id', $catIds)->pluck('product_id')->toArray();

        if(empty($productIDs)) return collect([]);

        // return Product::select('id','slug','title','condition','price','offer_price','offer_start','offer_end')
        return Product::whereIn('id', $productIDs)->available()->inRandomOrder()
        ->with([
            'feedbacks:rating,feedbackable_id,feedbackable_type',
            'image:path,imageable_id,imageable_type',
        ])
        ->limit($limit)->get();
    }

    /**
     * Get linked items of given item
     * @return array
     */
    public static function linked_items($item)
    {
        $linked_items = unserialize($item->linked_items);

        if(empty($linked_items)) return collect([]);

        // return Product::select('id','slug','title','condition','price','offer_price','offer_start','offer_end')
        return Product::with([
            'feedbacks:rating,feedbackable_id,feedbackable_type',
            'image:path,imageable_id,imageable_type',
        ])
        ->whereIn('id', $linked_items)->available()->get();
    }

    /**
     * Get given number of random products
     * @return array
     */
    public static function random_items($limit = 10)
    {
        return Product::with([
            'feedbacks:rating,feedbackable_id,feedbackable_type',
            'featuredImage:path,imageable_id,imageable_type',
        ])
        ->available()->inRandomOrder()->limit($limit)->get();
    }

    public static function recentlyViewedItems()
    {
        $products = session()->get('products.recently_viewed_items');

        if(!$products) return [];

        // return Product::select('id', 'slug', 'title')->whereIn('id', $products)
        return Product::whereIn('id', $products)
        ->available()->with(['images:path,imageable_id,imageable_type'])
        ->get();
    }

    /**
     * Get orders list for form dropdown.
     * @return array
     */
    public static function orders()
    {
        return Order::orderBy('order_number', 'asc')->pluck('order_number', 'id')->toArray();
    }

    /**
     * Get latest_orders list for merchnat.
     *
     * @return array
     */
    public static function latest_orders()
    {
        return Order::with('customer', 'status')->latest()->limit(10)->get();
    }

    /**
     * Get paid_orders list for form dropdown.
     *
     * @return array
     */
    public static function paid_orders()
    {
        return \DB::table('orders')->where('payment_status', Order::PAYMENT_STATUS_PAID)
        ->where('deleted_at', Null)->orderBy('order_number', 'asc')
        ->pluck('order_number', 'id')->toArray();
    }

    /**
     * Get order_statuses list for form dropdown.
     *
     * @return array
     */
    public static function order_statuses()
    {
        return \DB::table('order_statuses')->where('deleted_at', Null)->pluck('name', 'id');
    }

    /**
     * Get latest_stocks list for merchnat.
     *
     * @return array
     */
    public static function latest_stocks()
    {
        return Product::with('product', 'image:path,imageable_id,imageable_type')->latest()->limit(10)->get();
    }

    /**
     * Get low_qtt_stocks list for merchnat.
     *
     * @return array
     */
    public static function low_qtt_stocks()
    {
        return Product::lowQtt()->with('image:path,imageable_id,imageable_type')->latest()->limit(10)->get();
    }

    // /**
    //  * Get payment_statuses list for form dropdown.
    //  *
    //  * @return array
    //  */
    // public static function payment_statuses()
    // {
    //     return \DB::table('payment_statuses')->where('deleted_at', Null)->pluck('name', 'id');
    // }

    /**
     * Get address_types list for form dropdown.
     *
     * @return array
     */
    public static function address_types()
    {
        return \DB::table('address_types')->orderBy('id', 'asc')->pluck('type', 'type');
    }

    /**
     * Get payment_methods list for form dropdown.
     *
     * @return array
     */
    public static function payment_methods()
    {
        return \DB::table('payment_methods')->where('enabled', 1)->pluck('name', 'id');
    }

    /**
     * Get packagings list for form dropdown.
     *
     * @return array
     */
    public static function packagings()
    {
        return \DB::table('packagings')
        ->where('active', 1)->where('deleted_at', Null)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get country list for form dropdown.
     *
     * @return array
     */
    public static function countries()
    {
        return \DB::table('countries')->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get states list for form dropdown.
     *
     * @param  int $country_id
     *
     * @return array
     */
    public static function states($country_id = null)
    {
        $country_id = $country_id ?: config('system_settings.address_default_country');
        return \DB::table('states')->where('country_id', $country_id)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get currency list for form dropdown.
     *
     * @return array
     */
    public static function currencies()
    {
        $currencies = \DB::table('currencies')->select('name', 'iso_code', 'id')->where('active', 1)
        ->orderBy('priority', 'asc')->orderBy('name', 'asc')->get();
        // $currencies = \DB::table('currencies')->where('active', 1)->orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        $result = [];
        foreach ($currencies as $currency)
            $result[$currency->id] = $currency->name . ' (' . $currency->iso_code . ')';

        return $result;
    }

    /**
     * Get attributes list for form dropdown.
     *
     * @return array
     */
    public static function attributes()
    {
        return \DB::table('attributes')
        ->where('deleted_at', Null)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get attributes list with all values for form dropdown.
     *
     * @return array
     */
    public static function attributeWithValues()
    {
        return Attribute::where('deleted_at', Null)->with('attributeValues')->orderBy('order', 'asc')->get();
    }

    /**
     * Get attribute_types list for form dropdown.
     *
     * @return array
     */
    public static function attribute_types()
    {
        return \DB::table('attribute_types')->orderBy('type', 'asc')->pluck('type', 'id');
    }

    /**
     * Get manufacturers list for form dropdown.
     *
     * @return array
     */
    public static function manufacturers()
    {
        return \DB::table('manufacturers')->where('deleted_at', Null)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get gtin_types list for form dropdown.
     *
     * @return array
     */
    public static function gtin_types()
    {
        return \DB::table('gtin_types')->pluck('name', 'name');
    }

    /**
     * Get EmailTemplate list with all values for form dropdown.
     *
     * @return array
     */
    public static function email_templates()
    {
        return \DB::table('email_templates')->where('deleted_at', Null)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get banner_groups list for form dropdown.
     *
     * @return array
     */
    public static function banner_groups()
    {
        return \DB::table('banner_groups')->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get featured_categories list for form dropdown.
     *
     * @return array
     */
    public static function featured_categories()
    {
        return \DB::table('categories')->whereNull('deleted_at')->where('featured', true)->pluck('name', 'id');
    }

    /**
     * Get featured_categories list for theme.
     */
    public static function hot_categories()
    {
        return \DB::table('categories')->select('id','name','slug')->whereNull('deleted_at')->whereNotNull('featured')->get();
    }

    /**
     * Get pages list for theme.
     */
    public static function pages($visibility = Null)
    {
        if($visibility)
            return Page::select('title','slug','position')->published()->visibilityOf($visibility)->get();

        return Page::select('title','slug','position')->published()->get();
    }

    /**
     * Get cart_list list for customer.
     */
    // public static function cart_list()
    // {
    //     $customer = Auth::guard('customer')->user();
    //     // $customer->load('carts.inventories');
    //     // $carts = $customer->carts->inventories;

    //     $cart_list = \DB::table('carts')->join('cart_items', 'cart_items.cart_id', '=', 'carts.id')
    //     ->leftJoin('images', function ($join) {
    //         $join->on('images.imageable_id', '=', 'cart_items.inventory_id')->where('images.imageable_type', '=', 'App\Inventory');
    //     })
    //     ->select('cart_items.inventory_id as product_id','cart_items.item_description as product_name','cart_items.quantity as product_quantity','cart_items.unit_price as product_price','cart_items.inventory_id as unique_key','images.path as product_image')
    //     ->where('carts.customer_id', $customer->id)->whereNull('carts.deleted_at')->get();

    //     \Log::info($cart_list->toJson());
    //     return $cart_list;
    // }

    /**
     * Get tags list for form dropdown.
     *
     * @return array
     */
    public static function tags()
    {
        return \DB::table('tags')->orderBy('name', 'asc')->pluck('name', 'id');
    }

}