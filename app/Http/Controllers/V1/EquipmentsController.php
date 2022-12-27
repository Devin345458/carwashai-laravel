<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentGroup;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\Repair;
use App\Models\Store;
use Auth;
use BeyondCode\Comments\Comment;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;
use Spatie\Activitylog\Models\Activity;
use Throwable;

class EquipmentsController extends Controller
{
    /**
     * Returns all equipment for a store
     *
     * @param string|null $storeId
     * @return JsonResponse
     */
    public function getEquipment(string $storeId = null): JsonResponse
    {
        $equipment = Equipment::activeEquipment($storeId)
            ->with([
                'location' => function (BelongsTo $query) {
                    return $query->select([
                        'locations.id',
                        'locations.name',
                    ]);
                },
                'store' => function (BelongsTo $query) {
                    return $query->select([
                        'stores.id',
                        'stores.name',
                    ])->with(['image']);
                },
                'manufacturer' => function (BelongsTo $query) {
                    return $query->select([
                        'suppliers.id',
                        'suppliers.name',
                    ]);
                }
            ])
            ->select([
                'equipments.id',
                'equipments.name',
                'equipments.location_id',
                'equipments.store_id',
                'equipments.file_id',
                'equipments.position'
            ]);

        $equipment = $equipment->get();

        return response()->json(compact('equipment'));
    }

    /**
     * Add equipment to active store
     */
    public function add(): JsonResponse
    {
        $equipment = new Equipment(request()->input());
        $defaultLocation = Location::where([
            'store_id' => $equipment->store_id,
            'default_location' => true,
        ])->firstOrFail();
        $equipment->location_id = $defaultLocation->id;
        $equipment->save();

        return response()->json(compact('equipment'));
    }

    /**
     * @param int $id The id of the equipment to edit
     */
    public function edit(int $id): JsonResponse
    {
        $equipment = Equipment::whereId($id)->with('categories', 'location')->firstOrFail();
        $equipment->fill(request()->input());
        $equipment->save();

        $equipment->load('store', 'categories', 'location');
        return response()->json(compact('equipment'));
    }

    /**
     * Get equipment by id
     *
     * @param int $id The id of the equipment to get
     */
    public function view(int $id): JsonResponse
    {
        $equipment = Equipment::whereId($id)
            ->with('store', 'categories', 'location')
            ->withCount(['completed_maintenances' => function (Builder $query) {
                return $query->where('maintenance_maintenance_session.complete', 1);
            }])
            ->withCount(['repairs' => function (Builder $query) {
                return $query->where('repairs.status', Repair::STATUS_COMPLETE);
            }])->firstOrFail();

        return response()->json(compact('equipment'));
    }

    /**
     * Retrieve all activities on the equipment
     *
     * @param int $id The id of the equipment to get activities for
     */
    public function equipmentActivities(int $id): JsonResponse
    {
        $equipment = Equipment::find($id);
        $activityLogs = Activity::query()
            ->orWhere([
                'subject_type' => Equipment::class,
                'subject_id' => $equipment->id
            ])
            ->orWhere([
                'subject_type' => Repair::class,
                ['subject_id', 'IN' => $equipment->repairs()->pluck('id')->toArray()]
            ])
            ->orWhere([
                'subject_type' => Maintenance::class,
                ['subject_id', 'IN' => $equipment->maintenances()->pluck('id')->toArray()]
            ])
            ->orWhere([
                'subject_type' => Comment::class,
                ['subject_id', 'IN', $equipment->comments()->pluck('id')->toArray()]
            ])
            ->when($equipment->equipment_groups->pluck('id')->toArray(), function (Builder $query, array $equipmentGroupIds) {
                $query->orWhere([
                    'subject_type' => EquipmentGroup::class,
                    ['subject_id', 'IN', $equipmentGroupIds]
                ]);
            })
            ->with('causer')
            ->latest()
            ->paginate();

        return response()->json($activityLogs);
    }

    /**
     * Delete method
     * @param int $id Equipment id.
     */
    public function delete(int $id): JsonResponse
    {
        Equipment::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Retrieve a searched list of
     * @param string|null $store_id The store to search in
     */
    public function byStore(string $store_id = null): JsonResponse
    {
        $equipment = Equipment::activeEquipment($store_id)
            ->where('name', 'LIKE', '%' . request()->input('search') . '%')
            ->with('manufacturer', 'display_image')
            ->get();

        return response()->json(compact('equipment'));
    }

    /**
     * Catalogue method
     */
    public function catalogue(): JsonResponse
    {
        $catalogue = Equipment::whereHas('store', function (Builder $query) {
            return $query->whereIn('stores.company_id', [Auth::user()->company_id, 1]);
        })
            ->with('store', 'manufacturer', 'categories', 'maintenances')
            ->when(\request()->input('search'), function (Builder $query, $search) {
                $query->where('equipments.name', 'LIKE', '%' . $search . '%');
            })
            ->paginate();

        $catalogue->getCollection()->each(function (Equipment $equipment) {
            $equipment->quantity = 1;
        });

        return response()->json($catalogue);
    }

    public function reorder($locationId): JsonResponse
    {
        $equipmentIds = request()->input('equipmentIds');
        if (!count($equipmentIds)) {
            return response()->json(['success' => true]);
        }
        Equipment::whereIn('id', $equipmentIds)
            ->get()
            ->each
            ->fill(['location_id', $locationId])
            ->each
            ->save();

        Equipment::setNewOrder($equipmentIds);

        return response()->json(['success' => true]);
    }

    /**
     * @throws Throwable
     */
    public function copyEquipment(string $storeId, int $locationId): JsonResponse
    {
        DB::beginTransaction();

        try {
            $store = Store::findOrFail($storeId);
            $companyItems = Item::where('company_id', $store->company_id)->get();

            foreach (request()->input('equipment') as $equipment) {
                $oldEquipment = Equipment::whereId($equipment['id'])->firstOrFail();
                for ($i = 0; $i < $equipment['quantity']; $i++) {
                    $newEquipment = $oldEquipment->replicate();

                    $newEquipment->location_id = $locationId;
                    $newEquipment->name = $newEquipment->name . ' - Copy ' . ($i + 1);
                    $newEquipment->manufacturer_id = null;
                    $newEquipment->store_id = $storeId;
                    $newEquipment->position = null;
                    $newEquipment->save();

                    $originalMaintenances = $oldEquipment->maintenances()->with('items')->get();
                    foreach ($originalMaintenances as $maintenance) {
                        /** @var Maintenance $maintenance */
                        /** @var Maintenance $newMaintenance */
                        $newMaintenance = $maintenance->replicate();
                        $newMaintenance->store_id = $store->id;
                        $newMaintenance->maintainable_id = $newEquipment->id;
                        $newMaintenance->save();

                        foreach ($maintenance->items as $item) {
                            if (!$companyItems->where('name', $item->name)->first()) {
                                $item = $item->replicate();
                                $item->company_id = $store->company_id;
                                $item->save();
                                $companyItems->push($item);
                            } else {
                                $item = $companyItems->where('name', $item->name)->first();
                            }
                            $newMaintenance->items()->attach($item->id);

                            if (!$item->inventories->where('store_id', $storeId)->first()) {
                                $item->inventories()->create([
                                    'store_id' => $storeId,
                                    'cost' => 0,
                                    'supplier_id' => 0,
                                    'current_stock' => 0,
                                    'initial_stock' => 0,
                                    'desired_stock' => 0,
                                ]);
                            }
                        }
                    };
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
