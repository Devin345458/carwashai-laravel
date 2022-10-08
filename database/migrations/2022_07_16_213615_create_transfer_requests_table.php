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
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('to_store_id');
            $table->uuid('from_store_id');
            $table->integer('transfer_status_id');
            $table->integer('approved_by_id');
            $table->integer('order_item_id');
            $table->string('denial_reason');
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
        Schema::dropIfExists('transfer_requests');
    }
};
