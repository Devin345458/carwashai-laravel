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
        Schema::table('recordings', function (Blueprint $table) {
            $table->string('camera')->nullable()->change();
            $table->string('start_time')->nullable()->change();
            $table->string('end_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recordings', function (Blueprint $table) {
            $table->string('camera')->nullable(false)->change();
            $table->string('start_time')->nullable(false)->change();
            $table->string('end_time')->nullable(false)->change();
        });
    }
};
