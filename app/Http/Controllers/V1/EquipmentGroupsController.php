<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentGroup;
use App\Models\Maintenance;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class EquipmentGroupsController extends Controller
{
    /**
     * Index method
     */
    public function index(?string $storeId = null): JsonResponse
    {
        $equipmentGroups = EquipmentGroup::activeStore($storeId)
            ->withCount('equipments')
            ->paginate();

        return response()->json($equipmentGroups);
    }

    /**
     * View method
     *
     * @param int $id Equipment Group id.
     */
    public function view(int $id): JsonResponse
    {
        $equipmentGroup = EquipmentGroup::whereId($id)->with(
            'equipments.location',
            'maintenances'
        )->firstOrFail();

        return response()->json(compact('equipmentGroup'));
    }

    /**
     * Add method
     * @throws Throwable
     */
    public function add(): JsonResponse
    {
        try {
            DB::beginTransaction();
            $equipmentGroup = EquipmentGroup::create(request()->only(['name', 'store_id']));
            $equipmentGroup->equipments()->sync(collect(request('equipments'))->pluck('id')->toArray());
            $equipmentGroup->load(['equipments']);
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return response()->json(compact('equipmentGroup'));
    }

    /**
     * Edit method
     *
     * @param int $id Equipment Group id.
     */
    public function edit(int $id): JsonResponse
    {
        $equipmentGroup = EquipmentGroup::findOrFail($id);
        $equipmentGroup->fill(request(['name']));
        $equipments = collect(request('equipments'))->map(function ($equipmentData) {
            $equipment = Equipment::findOrNew($equipmentData['id']);
            return $equipment->fill($equipmentData);
        });
        $equipmentGroup->equipments()->saveMany($equipments->toArray());
        $maintenances = collect(request('maintenances'))->map(function ($maintenanceData) {
            $maintenance = Maintenance::findOrNew($maintenanceData['id']);
            return $maintenance->fill($maintenanceData);
        });
        $equipmentGroup->maintenances()->saveMany($maintenances->toArray());
        $equipmentGroup->push();
        return response()->json(compact('equipmentGroup'));
    }

    /**
     * Delete method
     *
     * @param EquipmentGroup $equipmentGroup
     * @return JsonResponse
     */
    public function delete(EquipmentGroup $equipmentGroup): JsonResponse
    {
        $equipmentGroup->delete();
        return response()->json(['success' => true]);
    }
}
