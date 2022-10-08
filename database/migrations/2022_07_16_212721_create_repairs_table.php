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
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('time')->default(0);
            $table->dateTime('due_date')->nullable();
            $table->integer('priority')->default(1);
            $table->float('health_impact')->nullable();
            $table->string('status')->default('Pending Assignment');
            $table->text('solution')->nullable();
            $table->uuid('store_id');
            $table->integer('equipment_id')->nullable();
            $table->integer('assigned_to_id')->nullable();
            $table->integer('assigned_by_id')->nullable();
            $table->dateTime('assigned_date')->nullable();
            $table->integer('maintenance_id')->nullable();
            $table->integer('repair_id')->nullable();
            $table->boolean('completed')->default(0);
            $table->string('completed_reason')->nullable();
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
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
