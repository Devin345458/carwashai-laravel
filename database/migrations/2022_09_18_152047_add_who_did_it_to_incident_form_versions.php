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
        Schema::table('incident_form_versions', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('store_id');
            $table->integer('version');
            $table->json('data');
            $table->uuid('created_by_id');
            $table->uuid('updated_by_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('incident_form_versions', function (Blueprint $table) {
            $table->string('name');
            $table->uuid('store_id');
            $table->dropColumn('version');
            $table->dropColumn('data');
            $table->dropColumn('created_by_id');
            $table->dropColumn('updated_by_id');
        });
    }
};
