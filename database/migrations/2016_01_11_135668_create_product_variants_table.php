<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->string('title')->nullable();
            $table->string('model_number')->nullable();
            $table->string('mpn')->nullable();
            $table->string('gtin')->nullable();
            $table->string('gtin_type')->nullable();
            $table->longtext('description')->nullable();
            $table->boolean('requires_shipping')->default(1)->nullable();
            $table->boolean('downloadable')->nullable();
            $table->bigInteger('image_id')->unsigned()->nullable();

            // From inventory
            $table->integer('warehouse_id')->unsigned()->nullable();
            $table->string('sku', 200)->nullable();
            // $table->tinyInteger('condition')->unsigned()->nullable();
            $table->enum('condition', ['New', 'Used', 'Refurbished'])->default('New');
            $table->text('condition_note')->nullable();
            $table->text('key_features')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('damaged_quantity')->nullable();

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

            $table->bigInteger('sale_count')->nullable();
            $table->boolean('active')->default(1);

            // $table->softDeletes();
            $table->timestamps();

            $table->foreign('image_id')->references('id')->on('images')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('attribute_product', function (Blueprint $table) {
            $table->integer('attribute_id')->unsigned()->index();
            $table->bigInteger('product_id')->unsigned()->index();
            $table->integer('attribute_value_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('attribute_value_id')->references('id')->on('attribute_values')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute_product');
        Schema::dropIfExists('product_variants');
    }
}
