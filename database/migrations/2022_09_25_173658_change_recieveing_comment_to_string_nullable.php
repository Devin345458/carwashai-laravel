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
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('receiving_comment')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->date('expected_delivery_date')->nullable()->change();
            $table->date('actual_delivery_date')->nullable()->change();
            $table->string('tracking_number')->nullable()->change();
            $table->string('received_by_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('receiving_comment')->nullable(false)->change();
            $table->integer('location')->nullable(false)->change();
            $table->date('expected_delivery_date')->nullable(false)->change();
            $table->date('actual_delivery_date')->nullable(false)->change();
            $table->integer('tracking_number')->nullable(false)->change();
            $table->integer('received_by_id')->nullable(false)->change();
        });
    }
};
