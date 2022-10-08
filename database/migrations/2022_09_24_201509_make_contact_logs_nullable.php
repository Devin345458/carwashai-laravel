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
        Schema::table('contact_logs', function (Blueprint $table) {
            $table->dateTime('when')->nullable()->change();
            $table->string('spoke_to')->nullable()->change();
            $table->string('details')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('contact_logs', function (Blueprint $table) {
            $table->dateTime('when')->nullable(false)->change();
            $table->string('spoke_to')->nullable(false)->change();
            $table->string('details')->nullable(false)->change();
        });
    }
};
