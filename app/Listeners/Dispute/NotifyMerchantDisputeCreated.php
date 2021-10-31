<?php

namespace App\Listeners\Dispute;

use App\System;
use App\Events\Dispute\DisputeCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Dispute\Created as DisputeCreatedNotification;

class NotifyMerchantDisputeCreated implements ShouldQueue
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
     * @param  DisputeCreated  $event
     * @return void
     */
    public function handle(DisputeCreated $event)
    {
        if(! config('system_settings')) {
            setSystemConfig();
        }

        // if(config('system_settings.notify_new_dispute')){
            $system = System::orderBy('id', 'asc')->first();
            $system->notify(new DisputeCreatedNotification($event->dispute));
        // }
    }
}
