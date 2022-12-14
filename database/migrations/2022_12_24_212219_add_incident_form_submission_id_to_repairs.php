<?php

use App\Models\IncidentFormSubmission;
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
        Schema::table('repairs', function (Blueprint $table) {
            $table->foreignIdFor(IncidentFormSubmission::class)->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->dropForeignIdFor(IncidentFormSubmission::class);
        });
    }
};
