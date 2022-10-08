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
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('file_id')->nullable();
            $table->integer('position');
            $table->integer('location_id');
            $table->uuid('store_id');
            $table->integer('manufacturer_id')->nullable();
            $table->integer('created_from_id')->nullable();
            $table->string('purchase_date')->nullable();
            $table->date('install_date')->nullable();
            $table->string('installer')->nullable();
            $table->string('warranty_expiration')->nullable();
            $table->string('model_number')->nullable();
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
        Schema::dropIfExists('equipments');
    }
};
