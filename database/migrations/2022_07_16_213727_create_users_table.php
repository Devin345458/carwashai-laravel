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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email');
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->dateTime('activation_date');
            $table->dateTime('tos_date');
            $table->boolean('active')->default(1);
            $table->string('role')->default('user');
            $table->string('company_id');
            $table->uuid('active_store_id')->nullable();
            $table->integer('file_id')->nullable();
            $table->string('about', 1000)->nullable();
            $table->string('time_zone')->nullable();
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
        Schema::dropIfExists('users');
    }
};
