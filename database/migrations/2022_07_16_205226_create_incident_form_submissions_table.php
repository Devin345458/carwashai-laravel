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
        Schema::create('incident_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->json('data');
            $table->integer('incident_form_version_id');
            $table->string('user_id');
            $table->enum('status', ['received', 'reviewing', 'contacting_client', 'getting_quote', 'denied', 'accepted'])->default('received');
            $table->uuid('store_id');
            $table->integer('progress');
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
        Schema::dropIfExists('incident_form_submissions');
    }
};
