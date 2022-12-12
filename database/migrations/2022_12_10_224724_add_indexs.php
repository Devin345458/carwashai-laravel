<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_counts', function (Blueprint $table) {
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->change();
            $table->foreign(['company_id'])->references(['id'])->on('companies')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('category_equipment', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->change();
            $table->unsignedBigInteger('equipment_id')->change();
            $table->foreign(['category_id'])->references(['id'])->on('categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['equipment_id'])->references(['id'])->on('equipments')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('completed_inventories', function (Blueprint $table) {
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('completed_procedure_steps', function (Blueprint $table) {
            $table->unsignedBigInteger('step_id')->change();
            $table->foreign(['step_id'])->references(['id'])->on('procedure_steps')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('contact_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('incident_form_submission_id')->change();
            $table->foreign(['incident_form_submission_id'])->references(['id'])->on('incident_form_submissions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('equipment_equipment_group', function (Blueprint $table) {
            $table->unsignedBigInteger('equipment_id')->change();
            $table->unsignedBigInteger('equipment_group_id')->change();
            $table->foreign(['equipment_id'])->references(['id'])->on('equipments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['equipment_group_id'])->references(['id'])->on('equipment_groups')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('equipment_file', function (Blueprint $table) {
            $table->unsignedBigInteger('equipment_id')->change();
            $table->unsignedBigInteger('file_id')->change();
            $table->foreign(['equipment_id'])->references(['id'])->on('equipments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['file_id'])->references(['id'])->on('files')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('equipment_groups', function (Blueprint $table) {
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('equipments', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->change();
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['file_id'])->references(['id'])->on('files')->nullOnDelete()->cascadeOnUpdate();
        });

        Schema::table('file_repair', function (Blueprint $table) {
            $table->unsignedBigInteger('repair_id')->change();
            $table->unsignedBigInteger('file_id')->change();
            $table->foreign(['file_id'])->references(['id'])->on('files')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['repair_id'])->references(['id'])->on('repairs')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('files', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->change();
            $table->foreign(['company_id'])->references(['id'])->on('companies')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('incident_form_submissions', function (Blueprint $table) {
            $table->unsignedBigInteger('incident_form_version_id')->change();
            $table->unsignedBigInteger('incident_form_id')->change();
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['incident_form_version_id'])->references(['id'])->on('incident_form_versions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['incident_form_id'])->references(['id'])->on('incident_forms')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('incident_form_versions', function (Blueprint $table) {
            $table->unsignedBigInteger('incident_form_id')->change();
            $table->foreign(['incident_form_id'])->references(['id'])->on('incident_forms')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('incident_forms', function (Blueprint $table) {
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->change();
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['item_id'])->references(['id'])->on('items')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_id')->change();
            $table->foreign(['inventory_id'])->references(['id'])->on('inventories')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('item_maintenance', function (Blueprint $table) {
            $table->unsignedBigInteger('maintenance_id')->change();
            $table->unsignedBigInteger('item_id')->change();
            $table->foreign(['maintenance_id'])->references(['id'])->on('maintenances')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['item_id'])->references(['id'])->on('items')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('item_repair', function (Blueprint $table) {
            $table->unsignedBigInteger('repair_id')->change();
            $table->unsignedBigInteger('item_id')->change();
            $table->foreign(['repair_id'])->references(['id'])->on('repairs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['item_id'])->references(['id'])->on('items')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('item_types', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->change();
            $table->foreign(['company_id'])->references(['id'])->on('companies')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('item_type_id')->change()->nullable();
            $table->unsignedBigInteger('company_id')->change();
            $table->unsignedBigInteger('file_id')->change();
            $table->foreign(['item_type_id'])->references(['id'])->on('item_types')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign(['company_id'])->references(['id'])->on('companies')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['file_id'])->references(['id'])->on('files')->nullOnDelete()->cascadeOnUpdate();
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('maintenance_maintenance_session', function (Blueprint $table) {
            $table->unsignedBigInteger('maintenance_id')->change();
            $table->unsignedBigInteger('maintenance_session_id')->change();
            $table->foreign(['maintenance_id'])->references(['id'])->on('maintenances')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['maintenance_session_id'])->references(['id'])->on('maintenance_sessions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('maintenance_sessions', function (Blueprint $table) {
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('maintenances', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->change();
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['file_id'])->references(['id'])->on('files')->nullOnDelete()->cascadeOnUpdate();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_id')->change();
            $table->foreign(['inventory_id'])->references(['id'])->on('inventories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('procedure_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('procedure_id')->change();
            $table->foreign(['procedure_id'])->references(['id'])->on('procedures')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('procedure_days', function (Blueprint $table) {
            $table->unsignedBigInteger('procedure_id')->change();
            $table->foreign(['procedure_id'])->references(['id'])->on('procedures')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('procedure_steps', function (Blueprint $table) {
            $table->unsignedBigInteger('procedure_id')->change();
            $table->foreign(['procedure_id'])->references(['id'])->on('procedures')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('procedures', function (Blueprint $table) {
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('recordings', function (Blueprint $table) {
            $table->unsignedBigInteger('incident_form_submission_id')->change();
            $table->foreign(['incident_form_submission_id'])->references(['id'])->on('incident_form_submissions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('repair_reminders', function (Blueprint $table) {
            $table->unsignedBigInteger('repair_id')->change();
            $table->foreign(['repair_id'])->references(['id'])->on('repairs')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('store_user', function (Blueprint $table) {
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['user_id'])->references(['id'])->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->change();
            $table->foreign(['company_id'])->references(['id'])->on('companies')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->change();
            $table->foreign(['store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['file_id'])->references(['id'])->on('files')->nullOnDelete()->cascadeOnUpdate();
        });

        Schema::table('transfer_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('order_item_id')->change();
            $table->foreign(['to_store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['from_store_id'])->references(['id'])->on('stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(['order_item_id'])->references(['id'])->on('order_items')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->change();
            $table->unsignedBigInteger('company_id')->change();
            $table->foreign(['active_store_id'])->references(['id'])->on('stores')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign(['file_id'])->references(['id'])->on('files')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign(['company_id'])->references(['id'])->on('companies')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_counts', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        Schema::table('category_equipment', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['equipment_id']);
        });

        Schema::table('completed_inventories', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('completed_procedure_steps', function (Blueprint $table) {
            $table->dropForeign(['step_id']);
        });

        Schema::table('contact_logs', function (Blueprint $table) {
            $table->dropForeign(['incident_form_submission_id']);
        });

        Schema::table('equipment_equipment_group', function (Blueprint $table) {
            $table->dropForeign(['equipment_id']);
            $table->dropForeign(['equipment_group_id']);
        });

        Schema::table('equipment_file', function (Blueprint $table) {
            $table->dropForeign(['equipment_id']);
        });

        Schema::table('equipment_groups', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('equipments', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('file_repair', function (Blueprint $table) {
            $table->dropForeign(['file_id']);
            $table->dropForeign(['repair_id']);
        });

        Schema::table('files', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        Schema::table('incident_form_submissions', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropForeign(['incident_form_version_id']);
            $table->dropForeign(['incident_form_id']);
        });

        Schema::table('incident_form_versions', function (Blueprint $table) {
            $table->dropForeign(['incident_form_id']);
        });

        Schema::table('incident_forms', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropForeign(['item_id']);
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
        });

        Schema::table('item_maintenance', function (Blueprint $table) {
            $table->dropForeign(['maintenance_id']);
            $table->dropForeign(['item_id']);
        });

        Schema::table('item_repair', function (Blueprint $table) {
            $table->dropForeign(['repair_id']);
            $table->dropForeign(['item_id']);
        });

        Schema::table('item_types', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['item_type_id']);
            $table->dropForeign(['company_id']);
            $table->dropForeign(['file_id']);
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('maintenance_maintenance_session', function (Blueprint $table) {
            $table->dropForeign(['maintenance_id']);
            $table->dropForeign(['maintenance_session_id']);
        });

        Schema::table('maintenance_sessions', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropForeign(['file_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropForeign(['store_id']);
        });

        Schema::table('procedure_assignments', function (Blueprint $table) {
            $table->dropForeign(['procedure_id']);
        });

        Schema::table('procedure_days', function (Blueprint $table) {
            $table->dropForeign(['procedure_id']);
        });

        Schema::table('procedure_steps', function (Blueprint $table) {
            $table->dropForeign(['procedure_id']);
        });

        Schema::table('procedures', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
        });

        Schema::table('recordings', function (Blueprint $table) {
            $table->dropForeign(['incident_form_submission_id']);
        });

        Schema::table('repair_reminders', function (Blueprint $table) {
            $table->dropForeign(['repair_id']);
        });

        Schema::table('store_user', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropForeign(['file_id']);
        });

        Schema::table('transfer_requests', function (Blueprint $table) {
            $table->dropForeign(['to_store_id']);
            $table->dropForeign(['from_store_id']);
            $table->dropForeign(['order_item_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['active_store_id']);
            $table->dropForeign(['file_id']);
            $table->dropForeign(['company_id']);
        });
    }
};
