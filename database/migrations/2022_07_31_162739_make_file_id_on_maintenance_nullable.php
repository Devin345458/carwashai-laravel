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
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->integer('file_id')->nullable()->change();
            $table->integer('frequency_cars')->default(0)->change();
            $table->integer('last_cars_completed')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->integer('file_id')->change();
            $table->integer('frequency_cars')->change();
            $table->integer('last_cars_completed')->change();
        });
    }
};
