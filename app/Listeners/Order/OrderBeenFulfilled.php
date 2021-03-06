<?php

namespace App\Listeners\Order;

use Notification;
use App\Events\Order\OrderFulfilled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Order\OrderFulfilled as OrderFulfilledNotification;

class OrderBeenFulfilled implements ShouldQueue
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderFulfilled  $event
     * @return void
     */
    public function handle(OrderFulfilled $event)
    {
        if ($event->notify_customer){
            if(!config('system_settings')) {
                setSystemConfig();
            }

            if ($event->order->customer_id) {
                $event->order->customer->notify(new OrderFulfilledNotification($event->order));
            }
            else if ($event->order->email){
                Notification::route('mail', $event->order->email)
                    // ->route('nexmo', '5555555555')
                    ->notify(new OrderFulfilledNotification($event->order));
            }
        }
    }
}
