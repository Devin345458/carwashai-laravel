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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activation_date');
            $table->dropColumn('tos_date');
            $table->uuid('active_store_id')->nullable()->change();
            $table->dropColumn('time_zone');
            $table->dropColumn('about');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('activation_date');
            $table->dateTime('tos_date');
            $table->uuid('active_store_id');
            $table->string('about', 1000)->nullable();
            $table->string('time_zone')->nullable();
            $table->timestamps();
        });
    }
};
