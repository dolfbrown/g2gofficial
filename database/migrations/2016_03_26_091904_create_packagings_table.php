<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packagings', function (Blueprint $table) {
            $table->increments('id');
            // $table->integer('shop_id')->unsigned()->nullable();
            $table->string('name');
            $table->decimal('width', 20, 6)->nullable();
            $table->decimal('height', 20, 6)->nullable();
            $table->decimal('depth', 20, 6)->nullable();
            $table->decimal('cost', 20, 6)->nullable();
            $table->boolean('default')->nullable();
            $table->boolean('active')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('packaging_product', function (Blueprint $table) {
            $table->integer('packaging_id')->unsigned()->index();
            $table->bigInteger('product_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('packaging_id')->references('id')->on('packagings')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packaging_product');
        Schema::dropIfExists('packagings');
    }
}
