<?php

namespace App\Listeners\Order;

use App\System;
use App\Events\Order\OrderCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Order\MerchantOrderCreatedNotification as OrderCreatedNotification;

class NotifyMerchantNewOrderPlaced implements ShouldQueue
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
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        if(!config('system_settings')) {
            setSystemConfig();
        }

        if(config('system_settings.notify_new_order')) {
            $system = System::orderBy('id', 'asc')->first();
            $system->notify(new OrderCreatedNotification($event->order));
        }
    }
}
