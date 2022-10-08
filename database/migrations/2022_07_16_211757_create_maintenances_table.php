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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('method');
            $table->integer('expected_duration');
            $table->integer('frequency_days');
            $table->integer('frequency_cars');
            $table->integer('file_id');
            $table->morphs('maintainable');
            $table->uuid('store_id');
            $table->date('last_completed_date');
            $table->integer('last_cars_completed');
            $table->string('procedures');
            $table->uuid('created_by_id');
            $table->uuid('updated_by_id');
            $table->softDeletes();
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
        Schema::dropIfExists('maintenances');
    }
};
