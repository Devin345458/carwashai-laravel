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
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('number')->nullable();
            $table->integer('file_id')->nullable();
            $table->integer('company_id');
            $table->string('address')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('country', 20)->nullable();
            $table->integer('zipcode')->nullable();
            $table->string('city')->nullable();
            $table->string('subscription_id')->nullable();
            $table->date('cancel_date')->nullable();
            $table->boolean('canceled')->default(0);
            $table->string('cancel_reason', 1000)->nullable();
            $table->string('setup_id')->nullable();
            $table->string('plan_id');
            $table->integer('store_type_id');
            $table->integer('current_car_count')->default(0);
            $table->boolean('allow_car_counts')->default(0);
            $table->integer('maintenance_due_days_offset')->default(0);
            $table->integer('maintenance_due_cars_offset')->default(0);
            $table->integer('upcoming_days_offset')->default(2);
            $table->integer('upcoming_cars_offset')->default(2000);
            $table->string('time_zone')->default('American/Chicago');
            $table->integer('require_scan');
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
        Schema::dropIfExists('stores');
    }
};
