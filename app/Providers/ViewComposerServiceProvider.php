<?php
namespace App\Providers;

use Auth;
use App\Product;
use App\SystemConfig;
use App\PaymentMethod;
use App\Charts\Visitors;
use App\Charts\Referrers;
use App\Charts\DeviceTypes;
use App\Charts\VisitorTypes;
use App\Charts\LatestSales;
use App\Charts\SalesByPeriod;
use App\Helpers\ListHelper;
use App\Helpers\Statistics;
use App\Helpers\CharttHelper;
use App\Charts\VisitorsOfMonths;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->composeAddressForm();

        $this->composeAdminNavigations();

        $this->composeAttributeForm();

        $this->composeAttributeValueForm();

        // $this->composeBillingSection();

        $this->composeBlogForm();

        $this->composeBannerForm();

        $this->composeCarrierForm();

        $this->composeCategoryForm();

        $this->composeCategorySubGroupForm();

        // $this->composeConfigPage();

        $this->composeCouponForm();

        $this->composeDashboardForAdmin();

        $this->composeDownloadableForm();
        // $this->composeDashboardForMerhcant();

        $this->composeGeneralConfigPage();

        $this->composeCreateOrderForm();

        $this->composeDisputeResponseForm();

        $this->composeEmailTemplatePartialForm();

        // $this->composeFaqTopicForm();

        $this->composeFaqForm();

        $this->composeFeaturedCategoriesForm();

        $this->composeManufacturerForm();

        $this->composeOrderFulfillmentForm();

        $this->composeOrderUpdateForm();

        $this->composePageForm();

        $this->composePaymentConfigPage();

        $this->composePlatformKpi();

        // $this->composeMerchantKpi();

        $this->composeProductForm();

        $this->composeRefundInitiationForm();

        if(SystemConfig::isGgoogleAnalyticEnabled() && SystemConfig::isGgoogleAnalyticConfigured()) {
            $this->composeReportAboutVisitors();
        }

        $this->composeRoleForm();

        $this->composeRoleShow();

        $this->composeSearchFilterForm();

        // $this->composeSearchCustomerForm();

        // $this->composeSetVariantForm();

        $this->composeShippingRateForm();

        $this->composeShippingZoneForm();

        // $this->composeShopForm();

        $this->composeSystemGeneralPage();

        $this->composeSystemConfigPage();

        $this->composeTaxForm();

        $this->composeThemeOption();

        // $this->composeTicketCreateForm();

        // $this->composeTicketStatusForm();

        // $this->composeTicketAssignForm();

        $this->composeUserForm();

        $this->composeVariantForm();

        $this->composeViewPaymentMethodsPage();

        $this->composeWarehouseForm();

    }

    /**
     * compose partial view of role form
     */
    private function composeAdminNavigations()
    {
        View::composer(

                'admin.header',

                function($view)
                {
                    $view->with('active_announcement', ListHelper::activeAnnouncement());
                });
    }

    /**
     * compose partial view of role form
     */
    private function composeRoleForm()
    {
        View::composer(

                'admin.role._form',

                function($view)
                {
                    $view->with('modules', ListHelper::modulesWithPermissions());
                });
    }

    /**
     * compose partial view of role
     */
    private function composeRoleShow()
    {
        View::composer(

                'admin.role._show',

                function($view)
                {
                    $view->with('modules', ListHelper::modulesWithPermissions());
                });
    }

    /**
     * compose partial view of user form
     */
    private function composeUserForm()
    {
        View::composer(

                'admin.user._form',

                function($view)
                {
                    $view->with('roles', ListHelper::roles());
                });
    }

    /**
     * compose partial view of attribute form
     */
    private function composeAttributeForm()
    {
        View::composer(

                'admin.attribute._form',

                function($view)
                {
                    $view->with('typeList', ListHelper::attribute_types());
                });
    }

    /**
     * compose partial view of attribute value form
     */
    private function composeAttributeValueForm()
    {
        View::composer(

                'admin.attribute-value._form',

                function($view)
                {
                    $view->with('attributeList', ListHelper::attributes());
                });
    }

    /**
     * compose partial view of category form
     */
    private function composeCategoryForm()
    {
        View::composer(

                'admin.category._form',

                function($view)
                {
                    $view->with('catList', ListHelper::catGrpSubGrpListArray());
                });
    }

    /**
     * compose partial view of CategorySubGroupForm form
     */
    private function composeCategorySubGroupForm()
    {
        View::composer(

                'admin.category._formSubGrp',

                function($view)
                {
                    $view->with('catGroups', ListHelper::categoryGrps());
                });
    }

    /**
     * compose partial view of shipping rate form
     */
    private function composeShippingRateForm()
    {
        View::composer(

                'admin.shipping_rate._form',

                function($view)
                {
                    $view->with('carriers', ListHelper::carriers());
                });
    }

    /**
     * compose partial view of shipping zone form
     */
    private function composeShippingZoneForm()
    {
        View::composer(

                'admin.shipping_zone._form',

                function($view)
                {
                    $view->with('taxes', ListHelper::taxes());
                    $view->with('countries', ListHelper::countries());
                });
    }

    /**
     * compose partial view of downloadable form
     */
    private function composeDownloadableForm()
    {
        View::composer(

                'admin.downloadable._form',

                function($view)
                {
                    $view->with('categories', ListHelper::catWithSubGrpListArray());

                    $view->with('manufacturers', ListHelper::manufacturers());

                    $view->with('countries', ListHelper::countries());

                    $view->with('attributes', ListHelper::attributeWithValues());

                    $view->with('suppliers', ListHelper::suppliers());

                    $view->with('products', ListHelper::products('downloadable'));

                    $view->with('tags', ListHelper::tags());
                });
    }

    /**
     * compose partial view of product form
     */
    private function composeProductForm()
    {
        View::composer(

                'admin.product._form',

                function($view)
                {
                    $view->with('categories', ListHelper::catWithSubGrpListArray());

                    $view->with('manufacturers', ListHelper::manufacturers());

                    $view->with('gtin_types', ListHelper::gtin_types());

                    $view->with('countries', ListHelper::countries());

                    $view->with('attributes', ListHelper::attributeWithValues());

                    $view->with('packagings', ListHelper::packagings());

                    $view->with('warehouses', ListHelper::warehouses());

                    $view->with('suppliers', ListHelper::suppliers());

                    $view->with('products', ListHelper::products());

                    $view->with('tags', ListHelper::tags());
                });
    }

    /**
     * compose partial view of product variant form
     */
    private function composeVariantForm()
    {
        View::composer(

                'admin.product._variant_form',

                function($view) {

                    $view->with('attributes', ListHelper::attributeWithValues());

                });
    }

    /**
     * compose partial view of genaral inventory form
     */
    // private function composeInventoryForm()
    // {
    //     View::composer(

    //             'admin.inventory._form',

    //             function($view)
    //             {
    //                 $view->with('attributes', ListHelper::attributeWithValues());

    //                 $view->with('packagings', ListHelper::packagings());

    //                 $view->with('warehouses', ListHelper::warehouses());

    //                 $view->with('suppliers', ListHelper::suppliers());

    //                 $view->with('inventories', ListHelper::inventories());

    //                 $view->with('tags', ListHelper::tags());
    //             });
    // }

    /**
     * compose partial view of inventory variant form
     */
    private function composeInventoryVariantForm()
    {
        View::composer(

                'admin.inventory._formWithVariant',

                function($view)
                {
                    $view->with('packagings', ListHelper::packagings());

                    $view->with('warehouses', ListHelper::warehouses());

                    $view->with('suppliers', ListHelper::suppliers());

                    $view->with('inventories', ListHelper::inventories());

                    $view->with('tags', ListHelper::tags());
                });
    }

    /**
     * compose partial view of set variant form
     */
    private function composeSetVariantForm()
    {
        View::composer(

                'admin.inventory._set_variant',

                function($view)
                {
                    $view->with('attributes', ListHelper::attributeWithValues());
                });
    }

    /**
     * compose partial view of tax form
     */
    private function composeTaxForm()
    {
        View::composer(

            'admin.tax._form',

            function($view)
            {
                $view_data = $view->getData();

                $country_id = isset($view_data['tax']) ?
                        $view_data['tax']['country_id'] :
                        config('system_settings.address_default_country');

                $view->with('countries', ListHelper::countries());

                $view->with('states', ListHelper::states($country_id));
            }
        );
    }

    /**
     * compose partial view of Email template partial form
     */
    private function composeEmailTemplatePartialForm()
    {
        View::composer(

                'admin.partials._email_template_id_field',

                function($view)
                {
                    $view->with('email_templates', ListHelper::email_templates());
                });
    }

    /**
     * compose partial view of FAQ template partial form
     */
    private function composeFaqForm()
    {
        View::composer(

                'admin.faq._form',

                function($view)
                {
                    $view->with('topics', ListHelper::faq_topics());
                });
    }

    /**
     * compose partial view of manufacturer form
     */
    private function composeManufacturerForm()
    {
        View::composer(

            'admin.manufacturer._form',

            function($view)
            {
                $view->with('countries', ListHelper::countries());
            }
        );
    }

    /**
     * compose partial view of warehouse form
     */
    private function composeWarehouseForm()
    {
        View::composer(

            'admin.warehouse._form',

            function($view)
            {
                $view->with('users', ListHelper::users());
            }
        );
    }

    /**
     * compose partial view of carrier form
     */
    private function composeCarrierForm()
    {
        View::composer(

            'admin.carrier._form',

            function($view)
            {
                $view->with('taxes', ListHelper::taxes());
            }
        );
    }

    /**
     * compose partial view of address form
     */
    private function composeAddressForm()
    {
        View::composer(

            'address._form',

            function($view)
            {
                $view_data = $view->getData();

                $country_id = isset($view_data['address']) ?
                        $view_data['address']['country_id'] :
                        config('system_settings.address_default_country');

                if (
                    isset($view_data['addressable_type']) && 'App\Customer' == $view_data['addressable_type']
                    ||
                    isset($view_data['address']) && 'App\Customer' == $view_data['address']['addressable_type']
                    )
                {
                    $view->with('address_types', ListHelper::address_types());
                }

                $view->with('countries', ListHelper::countries());

                $view->with('states', ListHelper::states($country_id));
            }
        );
    }

    /**
     * compose partial view of invoice and order filter
     */
    private function composeSearchFilterForm()
    {
        View::composer(

            'admin.partials._filter',

            function($view)
            {
                $view->with('statuses', ListHelper::order_statuses());
                $view->with('payments', ListHelper::payment_statuses());
            }
        );
    }

    /**
     * compose partial view of invoice and order filter
     */
    private function composeCreateOrderForm()
    {
        View::composer(

            'admin.order.create',

            function($view)
            {
                $view->with('payment_statuses', ListHelper::payment_statuses());
                $view->with('payment_methods', ListHelper::payment_methods());

                $inventories = Product::available()->get();

                foreach ($inventories as $inventory){
                    $items[$inventory->id] = $inventory->sku .': '. $inventory->title . ' - ' . $inventory->condition;

                    if ($inventory->featuredImage) {
                        $img_path = optional($inventory->featuredImage)->path;
                    }
                    else {
                        $img_path = optional($inventory->image)->path;
                    }

                    $product_info[$inventory->id] = [
                        "id" => $inventory->id,
                        "image" => $img_path,
                        "salePrice" => round($inventory->price, 2),
                        "offerPrice" => round($inventory->offer_price, 2),
                        "stockQtt" => $inventory->stock_quantity,
                        "shipping_weight" => $inventory->shipping_weight,
                        "offerStart" => $inventory->offer_start,
                        "offerEnd" => $inventory->offer_end,
                    ];
                }

                $view->with('products', isset($items) ? $items : []);
                $view->with('inventories', isset($product_info) ? $product_info : []);
            }
        );
    }

    /**
     * compose partial view of order fulfillment
     */
    private function composeOrderFulfillmentForm()
    {
        View::composer(

            'admin.order._fulfill',

            function($view)
            {
                $view->with('carriers', ListHelper::carriers());
            }
        );
    }

    /**
     * compose partial view of order status update
     */
    private function composeOrderUpdateForm()
    {
        View::composer(

            'admin.order._edit',

            function($view)
            {
                $view->with('order_statuses', ListHelper::order_statuses());
            }
        );
    }

    /**
     * compose partial view of ticket create form
     */
    private function composeTicketCreateForm()
    {
        View::composer(

            'admin.account._create_ticket',

            function($view)
            {
                $view->with('ticket_categories', ListHelper::ticket_categories());
                $view->with('priorities', ListHelper::ticket_priorities());
            }
        );
    }

    /**
     * compose partial view of ticket status form
     */
    private function composeTicketStatusForm()
    {
        View::composer(

            'admin.ticket._status_form',

            function($view)
            {
                $view->with('priorities', ListHelper::ticket_priorities());
                $view->with('statuses', ListHelper::ticket_statuses_all());
            }
        );
    }

    /**
     * compose partial view of ticket status form
     */
    private function composeTicketAssignForm()
    {
        View::composer(

            'admin.ticket._assign',

            function($view)
            {
                $view->with('users', ListHelper::platform_users());
            }
        );
    }

    /**
     * compose partial view of dispute status form
     */
    private function composeDisputeResponseForm()
    {
        View::composer(

            'admin.dispute._response',

            function($view)
            {
                $view->with('statuses', ListHelper::dispute_statuses());
            }
        );
    }

    private function composeRefundInitiationForm()
    {
        View::composer(

            'admin.refund._initiate',

            function($view)
            {
                $view->with('orders', ListHelper::paid_orders());
                $view->with('statuses', ListHelper::refund_statuses());
            }
        );
    }

    /**
     * compose Coupon Form
     */
    private function composeCouponForm()
    {
        View::composer(

            'admin.coupon._form',

            function($view)
            {
                $view->with([
                    'shipping_zones' => ListHelper::shipping_zones(),
                ]);
            });
    }

    /**
     * compose page create form
     */
    private function composePageForm()
    {
        View::composer(

            'admin.page._form',

            function($view)
            {
                $view->with([
                    'positions' => ListHelper::page_positions(),
                ]);
            });
    }

    /**
     * compose KPI report
     */
    private function composePlatformKpi()
    {
        View::composer(

        'admin.report.platform.kpi',

        function($view)
        {
            // Charts
            $start = CharttHelper::getStartDate();
            $end = $start->copy()->subMonths(config('charts.default.months'))->startOfMonth();

            $chart = new SalesByPeriod($start, $end, 'M');

            $salesData = Statistics::sales_data_by_period($start, $end);

            // Preparing Sales amount dataset
            $salesTotal = CharttHelper::prepareSaleTotal($salesData, 'M');
            foreach ($chart->labels as $key => $label)
                $dataset[$key] = array_key_exists($label, $salesTotal) ? round($salesTotal[$label]) : 0;

            $chart->dataset(trans('app.sale'), 'column', $dataset);

            $period = $start->diffInDays($end);

            $view->with([
                'chart' => $chart,
                'top_listings'                 => ListHelper::top_listing_items(10),
                'top_suppliers'                => ListHelper::top_suppliers(5),
                'top_categories'               => ListHelper::top_categories(5),
                'top_customers'                => ListHelper::top_customers(10),
                'returning_customers'          => ListHelper::returning_customers(10),
                'orders_count'                 => Statistics::latest_order_count($period),
                'abandoned_carts_count'        => Statistics::abandoned_carts_count($period),
                'latest_refund_total'          => Statistics::latest_refund_total($period),
                'sales_total'                  => $salesData->sum('total'),
                'discount_total'               => $salesData->sum('discount'),
            ]);
        });
    }

    /**
     * compose visitors report for admin
     */
    private function composeReportAboutVisitors()
    {
        View::composer(

            'admin.report.platform.visitors',

            function($view)
            {
                $months = config('charts.google_analytic.period');

                // DeviceTypes Chart
                $topDevicesData = CharttHelper::topDevice($months);
                $chartDevices = new DeviceTypes($topDevicesData->pluck('deviceCategory'));
                $chartDevices->dataset(trans('app.sessions'), 'pie', $topDevicesData->pluck('sessions')->toArray())->color(['#e4d354', '#7CB5EC']);

                // VisitorTypes Chart
                $visitorTypesData = CharttHelper::userTypes($months);
                $chartVisitorTypes = new VisitorTypes($visitorTypesData->pluck('type'));
                $chartVisitorTypes->dataset(trans('app.sessions'), 'pie', $visitorTypesData->pluck('sessions')->toArray())->color(['#7CB5EC', '#FFBC75']);

                // Referrers Chart
                $referrersData = CharttHelper::topReferrers($months);
                $chartReferrers = new Referrers($referrersData->pluck('url'));
                $chartReferrers->dataset(trans('app.page_views'), 'bar', $referrersData->pluck('pageViews'));

                // Visitors Chart
                $visitorsData = CharttHelper::fetchVisitorsOfMonthsFromGoogle($months);
                $chartVisitors = new Visitors($months);
                $chartVisitors->dataset(trans('app.page_views'), 'column', $visitorsData['page_views'])->color(config('charts.visitors.colors.page_views'));
                $chartVisitors->dataset(trans('app.sessions'), 'column', $visitorsData['sessions'])->color(config('charts.visitors.colors.sessions'));
                $chartVisitors->dataset(trans('app.unique_visits'), 'column', $visitorsData['visits'])->color(config('charts.visitors.colors.unique_visits'));

                $view->with([
                    'chartVisitors' => $chartVisitors,
                    'chartReferrers' => $chartReferrers,
                    'chartVisitorTypes' => $chartVisitorTypes,
                    'chartDevices' => $chartDevices,
                ]);
            });
    }

    /**
     * compose dashboard UI for admin
     */
    private function composeDashboardForAdmin()
    {
        View::composer(

                'admin.dashboard.platform',

                function($view)
                {
                    // Charts
                    $months = config('charts.visitors.months');
                    $days = config('charts.latest_sales.days');

                    $salesData = CharttHelper::SalesOfLast($days);
                    $visitorsData = CharttHelper::visitorsOfMonths($months);

                    $today = \Carbon\Carbon::today()->startOfMonth();
                    $day_count = $today->diffInDays($today->copy()->subMonths($months));
                    $per_day_visits = round( array_sum($visitorsData['visits']) / $day_count );

                    $dispute_count = Statistics::dispute_count();
                    $refund_request_count = Statistics::open_refund_request_count();

                    $salesChart = new LatestSales($days);
                    $salesChart->dataset(trans('app.sale'), 'column', $salesData)->color(config('charts.latest_sales.color'));

                    $visitorsChart = new VisitorsOfMonths($months, $per_day_visits);
                    $visitorsChart->dataset(trans('app.page_views'), 'areaspline', $visitorsData['page_views'])->color(config('charts.visitors.colors.page_views'));

                    if(array_key_exists('sessions', $visitorsData)){
                        $visitorsChart->dataset(trans('app.sessions'), 'areaspline', $visitorsData['sessions'])->color(config('charts.visitors.colors.sessions'));
                    }

                    $visitorsChart->dataset(trans('app.unique_visits'), 'areaspline', $visitorsData['visits'])->color(config('charts.visitors.colors.unique_visits'));

                    $view->with([
                        'visitorsChart' => $visitorsChart,
                        'salesChart' => $salesChart,

                        'customer_count'            => Statistics::customer_count(),
                        'new_customer_last_30_days' => Statistics::customer_count(30),

                        'todays_sale_amount'        => Statistics::todays_sale_amount(),
                        'yesterdays_sale_amount'    => Statistics::yesterdays_sale_amount(),

                        'todays_visitor_count'      => Statistics::visitor_count('today'),
                        'last_30days_visitor_count' => Statistics::visitor_count(30),
                        'last_60days_visitor_count' => Statistics::visitor_count(60),

                        'appealed_dispute_count'    => Statistics::appealed_dispute_count(),
                        'last_30days_appealed_dispute_count' => Statistics::appealed_dispute_count(Null, 30),
                        'last_60days_appealed_dispute_count' => Statistics::appealed_dispute_count(Null, 60),

                        'latest_refund_total'       => Statistics::latest_refund_total($days),
                        'latest_sale_total'         => array_sum($salesData),

                        'unfulfilled_order_count'   => Statistics::unfulfilled_order_count(),
                        'stock_out_count'           => Statistics::stock_out_count(),
                        'todays_order_count'        => Statistics::todays_order_count(),
                        'last_sale'                 => Statistics::last_sale(),

                        'dispute_count'             => $dispute_count,
                        'refund_request_count'      => $refund_request_count,
                        'stock_count'               => Statistics::products_count(),
                        'latest_order_count'        => Statistics::latest_order_count($days),
                        'latest_orders'             => ListHelper::latest_orders(),
                        'top_customers'             => ListHelper::top_customers(),
                        'top_listings'              => ListHelper::top_listing_items(),
                        'latest_products'           => ListHelper::latest_products(),
                        'low_qtt_stocks'            => ListHelper::low_qtt_stocks(),
                    ]);

                    // Disputs and Refunds widget (optional)
                    // if($dispute_count > 0 || $refund_request_count > 0){
                        $view->with([
                            'last_30days_dispute_count'  => Statistics::dispute_count(30),
                            'last_60days_dispute_count'  => Statistics::dispute_count(60),
                            'last_30days_refund_request_count'  => Statistics::refund_request_count(30),
                            'last_60days_refund_request_count'  => Statistics::refund_request_count(60)
                        ]);
                    // }
                });
    }

    /**
     * compose partial view of Config Page
     */
    private function composeViewPaymentMethodsPage()
    {
        View::composer(

                'admin.config.payment-method.index',

                function($view)
                {
                    $view->with('payment_method_types', ListHelper::payment_method_types());
                    $view->with('payment_methods', PaymentMethod::where('enabled', 1)->get());
                    // $view->with('config', Config::findOrFail(auth()->user()->merchantId()));
                });
    }

    /**
     * compose partial view of SystemConfig Page
     */
    private function composeSystemConfigPage()
    {
        View::composer(

                'admin.system.config',

                function($view)
                {
                    $view->with('countries', ListHelper::countries());
                    $view->with('states', ListHelper::states());
                });
    }


    /**
     * compose partial view of SystemConfig Page
     */
    private function composePaymentConfigPage()
    {
        View::composer(

                'admin.system.payment_methods',

                function($view)
                {
                    $view->with('payment_method_types', ListHelper::payment_method_types());
                    $view->with('payment_methods', PaymentMethod::all());
                });
    }

    /**
     * compose partial view of GeneralConfig Page
     */
    private function composeGeneralConfigPage()
    {
        View::composer(

                'admin.config.general',

                function($view)
                {
                    $view->with('timezones', ListHelper::timezones());
                });
    }

    /**
     * compose partial view of General System Config Page
     */
    private function composeSystemGeneralPage()
    {
        View::composer(

                'admin.system.general',

                function($view)
                {
                    $view->with('timezones', ListHelper::timezones());
                    $view->with('currencies', ListHelper::currencies());
                });
    }

    /**
     * compose partial view of banner form
     */
    private function composeBannerForm()
    {
        View::composer(

            'admin.banner._form',

            function($view)
            {
                $view->with('groups', ListHelper::banner_groups());
            }
        );
    }

    /**
     * compose partial view of Theme Option
     */
    private function composeThemeOption()
    {
        View::composer(

            'admin.theme.options',

            function($view)
            {
                $view->with('featured_categories', ListHelper::featured_categories());
            }
        );
    }

    /**
     * compose partial view of Theme Option
     */
    private function composeFeaturedCategoriesForm()
    {
        View::composer(

            'admin.theme._edit_featured_categories',

            function($view)
            {
                $view->with('categories', ListHelper::categories());
                $view->with('featured_categories', ListHelper::featured_categories()->toArray());
            }
        );
    }

    /**
     * compose partial view of blog form
     */
    private function composeBlogForm()
    {
        View::composer(

            'admin.blog._form',

            function($view)
            {
                $view->with('tags', ListHelper::tags());
            }
        );
    }
}
