<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->integer('order_item_status_id');
            $table->integer('inventory_id');
            $table->integer('received_by_id');
            $table->integer('receiving_comment');
            $table->integer('shipping_slip');
            $table->integer('location');
            $table->date('expected_delivery_date');
            $table->date('actual_delivery_date');
            $table->integer('tracking_number');
            $table->float('purchase_cost');
            $table->uuid('store_id');
            $table->string('method');
            $table->uuid('created_by_id');
            $table->uuid('updated_by_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
