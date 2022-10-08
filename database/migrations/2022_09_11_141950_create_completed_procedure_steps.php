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
        Schema::create('completed_procedure_steps', function (Blueprint $table) {
            $table->id();
            $table->integer('step_id');
            $table->date('date');
            $table->text('note')->nullable();
            $table->boolean('completed')->default(false);
            $table->uuid('completed_by_id')->nullable();
            $table->timestamp('completed_at')->nullable();
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
        Schema::dropIfExists('completed_procedure_steps');
    }
};
