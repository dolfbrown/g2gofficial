<?php

namespace App\Helpers;

use Auth;
use App\User;
use App\Cart;
use App\Order;
use App\Refund;
use App\Visitor;
use App\Dispute;
use App\Message;
use App\Customer;
use App\Product;
use Carbon\Carbon;

/**
* Provide statistics all over the application
*/
class Statistics
{
    public static function visitor_count($period = Null)
    {
        $visitor = new Visitor;

        if ($period == Null)
            return $visitor->count();
        else if(is_numeric($period))
            $date = Carbon::today()->subDays($period)->startOfDay();
        else if ($period == 'today')
            $date = Carbon::today()->startOfDay();
        else
            $date = Carbon::today()->startOfDay();

        return $visitor->of($date)->count();
    }

    public static function customer_count($period = null)
    {
        $customer = new Customer;

        if ($period){
            $date = Carbon::today()->subDays($period);

            $customer = $customer->where('created_at', '>=', $date);
        }

        return $customer->count();
    }

    public static function stock_out_count()
    {
        return Product::stockOut()->count();
    }

    public static function customer_orders_count($customer)
    {
        return Order::withTrashed()->where('customer_id', $customer)->count();
    }

    public static function sold_items_count()
    {
        return \DB::table('order_items')->join('orders', 'orders.id', '=', 'order_items.order_id')
        ->select('order_items.quantity')->sum('order_items.quantity');
    }

    public static function total_spent($customer)
    {
        return Order::withTrashed()->where('customer_id', $customer)->sum('total');
    }

    public static function last_sale()
    {
        return Order::withTrashed()->orderBy('created_at', 'desc')->first();
    }

    public static function todays_sale_amount()
    {
        return Order::withTrashed()->whereDate('created_at', \Carbon\Carbon::today())->sum('total');
    }

    public static function yesterdays_sale_amount()
    {
        return Order::withTrashed()->whereDate('created_at', \Carbon\Carbon::yesterday())->sum('total');
    }

    public static function sales_data_by_period(Carbon $startTime, Carbon $endTime)
    {
        return Order::select('total', 'discount', 'created_at')
        ->withTrashed() //Include the arcived orders also
        ->where('created_at', '<=' , $startTime)
        ->where('created_at', '>=' , $endTime)
        ->orderBy('created_at', 'DESC')
        ->get();
    }

    public static function products_count()
    {
        return Product::count();
    }

    public static function latest_refund_total($period = 15)
    {
        return Refund::statusOf(Refund::STATUS_APPROVED)
        ->whereDate('updated_at', '>=', Carbon::today()->subDays($period))->sum('amount');
    }

    public static function latest_order_count($period = 15)
    {
        return Order::withTrashed()
        ->whereDate('created_at', '>=', Carbon::today()->subDays($period))->count();
    }

    public static function todays_order_count()
    {
        return Order::withTrashed()->whereDate('created_at', \Carbon\Carbon::today())->count();
    }

    // public static function orders_count_by_period($from = '', $to = '')
    // {
    //     return Order::withTrashed()->whereDate('created_at', '>=', Carbon::today()->subDays($period))->count();
    // }

    public static function unfulfilled_order_count()
    {
        return Order::unfulfilled()->count();
    }

    public static function abandoned_carts_count($period = 15)
    {
        return Cart::whereDate('created_at', '>=', Carbon::today()->subDays($period))->count();
    }

    public static function unread_msg_count()
    {
        return \DB::table('messages')->where('label', Message::LABEL_INBOX)
                    ->where('status', '<', Message::STATUS_READ)->count();
    }

    public static function draft_msg_count()
    {
        return \DB::table('messages')->where('label', Message::LABEL_DRAFT)->count();
    }

    public static function spam_msg_count()
    {
        return \DB::table('messages')->where('label', Message::LABEL_SPAM)->count();
    }

    public static function trash_msg_count()
    {
        return \DB::table('messages')->where('label', Message::LABEL_TRASH)->count();
    }

    public static function open_refund_request_count()
    {
        return Refund::open()->count();
    }

    public static function refund_request_count($period = Null)
    {
        $refund = new Refund;

        if ($period){
            $date = Carbon::today()->subDays($period);

            $refund = $refund->where('created_at', '>=', $date);
        }

        return $refund->count();
    }

    public static function dispute_count($period = null)
    {
        $dispute = new Dispute;

        if ($period){
            $date = Carbon::today()->subDays($period);

            $dispute = $dispute->where('created_at', '>=', $date);
        }

        return $dispute->open()->count();
    }

    public static function appealed_dispute_count($period = Null)
    {
        $dispute = new Dispute;

        if ($period){
            $date = Carbon::today()->subDays($period);

            // If include all disputes of every statuses
            return $dispute->where('created_at', '>=', $date)->count();
        }

        return $dispute->statusOf(Dispute::STATUS_APPEALED)->count();
    }

    public static function disputes_by_customer_count($customer, $period = null)
    {
        if($period){
            $date = Carbon::today()->subDays($period);

            return \DB::table('disputes')->where('customer_id', $customer)->where('created_at', '>=', $date)->count();
        }

        return \DB::table('disputes')->where('customer_id', $customer)->count();
    }
}