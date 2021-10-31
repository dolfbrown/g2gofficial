<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('manufacturer_id')->unsigned()->nullable();
            $table->string('brand')->nullable();
            $table->string('title');
            $table->string('model_number')->nullable();
            $table->string('mpn')->nullable();
            $table->string('gtin')->nullable();
            $table->string('gtin_type')->nullable();
            $table->longtext('description')->nullable();
            $table->integer('origin_country')->unsigned()->nullable();
            $table->boolean('requires_shipping')->default(1)->nullable();
            $table->boolean('downloadable')->nullable();

            // From inventory
            $table->integer('warehouse_id')->unsigned()->nullable();
            $table->integer('supplier_id')->unsigned()->nullable();
            $table->string('sku', 200);
            // $table->tinyInteger('condition')->unsigned()->nullable();
            $table->enum('condition', ['New', 'Used', 'Refurbished'])->default('New');
            $table->text('condition_note')->nullable();
            $table->text('key_features')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('damaged_quantity')->nullable();

            // $table->integer('tax_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();

            $table->decimal('purchase_price', 20, 6)->nullable();
            $table->decimal('price', 20, 6);
            $table->decimal('offer_price', 20, 6)->nullable();
            $table->timestamp('offer_start')->nullable();
            $table->timestamp('offer_end')->nullable();

            // $table->integer('packaging_id')->unsigned()->nullable();
            // $table->decimal('shipping_width', 20, 2)->nullable();
            // $table->decimal('shipping_height', 20, 2)->nullable();
            // $table->decimal('shipping_depth', 20, 2)->nullable();
            $table->decimal('shipping_weight', 20, 2)->nullable();
            $table->boolean('free_shipping')->nullable();

            $table->timestamp('available_from')->useCurrent();
            $table->integer('min_order_quantity')->default(1);
            $table->text('linked_items')->nullable();
            $table->boolean('stuff_pick')->nullable();

            $table->string('slug')->unique();
            $table->text('meta_title')->nullable();
            $table->longtext('meta_description')->nullable();
            $table->bigInteger('sale_count')->default(0);
            $table->boolean('active')->default(1);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('gtin_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->text('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gtin_types');
        Schema::dropIfExists('products');
    }
}
