<?php

namespace App\Events\Inventory;

use App\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class InventoryLow
{
    use Dispatchable, SerializesModels;

    public $inventory;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->inventory = $product;
    }
}
