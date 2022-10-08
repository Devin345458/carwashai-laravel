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
        Schema::table('incident_form_submissions', function (Blueprint $table) {
            $table->text('assessment');
            $table->text('customer_resolution');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('incident_form_submissions', function (Blueprint $table) {
            $table->dropColumn('assessment');
            $table->dropColumn('customer_resolution');
        });
    }
};
