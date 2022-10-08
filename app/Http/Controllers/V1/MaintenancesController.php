<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentGroup;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Location;
use App\Models\Maintenance;
use Auth;
use DB;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class MaintenancesController extends Controller
{
    /**
     * Add method
     */
    public function add(): JsonResponse
    {
        $maintenance = Maintenance::create(request()->except('maintenance.items'));
        $items = [];
        foreach (request('items') as $item) {
            $items[$item['id']] = ['quantity' => $item['pivot']['quantity']];
        }
        $maintenance->items()->sync($items);

        return response()->json(['success' => true]);
    }

    /**
     * Edit method
     *
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->fill(request()->input());
        $maintenance->save();

        $items = [];
        foreach (request('items') as $item) {
            $items[$item['id']] = ['quantity' => $item['quantity']];
        }
        $maintenance->items()->sync($items);

        return response()->json(['success' => true]);
    }

    /**
     * Delete method
     *
     * @param  int $id Maintenance id.
     */
    public function delete(int $id)
    {

        $maintenance = Maintenance::findOrFail($id);
        $maintenance->delete();
        return response()->json([
            'success' => true,
            'message' => 'The Maintenance has been deleted.'
        ]);
    }

    /**
     * Returns store maintenance grouped by due and upcoming and grouped equipment and sorted by equipment order
     * @param string $storeId
     * @return JsonResponse
     */
    public function storesMaintenance(string $storeId): JsonResponse
    {
        $dueMaintenance = Maintenance::dueEquipmentMaintenance($storeId, true);
        $upcomingMaintenance = Maintenance::dueEquipmentMaintenance($storeId, false);
        return response()->json(compact('dueMaintenance', 'upcomingMaintenance'));
    }

    public function getMaintenances(): JsonResponse
    {
        $maintenances = Maintenance::where(['maintenances.store_id' => Auth::user()->active_store_id])
            ->with([
                'items.inventories',
                'maintainable',
            ]);
        return response()->json(['maintenances' => $maintenances]);
    }

    /**
     * @throws Exception
     */
    public function dashboardWidget($storeId = null): JsonResponse
    {
        $dueMaintenance = Maintenance::dueEquipmentMaintenance($storeId, true);
        $upcomingMaintenance = Maintenance::dueEquipmentMaintenance($storeId, false);

        return response()->json([
            'dueMaintenance' => collect($dueMaintenance)->reduce(fn(Location $location) => count($location->maintenances)),
            'upcomingMaintenance' => collect($upcomingMaintenance)->reduce(fn(Location $location) => count($location->maintenances)),
        ]);
    }

    public function getMaintenance($id): JsonResponse
    {
        $maintenance = Maintenance::find($id)->with(['maintainable', 'items.active_store_inventory']);
        return response()->json(compact('maintenance'));
    }

    public function catalogue(): JsonResponse
    {
        $catalogue = Maintenance::whereHas('store', function (Builder $query) {
                return $query->whereIn('stores.company_id', [Auth::user()->company_id, 1]);
            })
            ->with([
                'maintainable',
                'items',
                'store',
            ])
            ->get();

        return response()->json(compact('catalogue'));
    }

    public function getMaintenancesByIds(): JsonResponse
    {
        $ids = request()->input('ids');
        $maintenances = Maintenance::whereIn('id', $ids)
            ->with('items.inventories');

        return response()->json(['maintenances' => $maintenances]);
    }

    public function view(int $id): JsonResponse
    {
        $maintenance = Maintenance::find($id)->load(['items.active_store_inventory', 'maintainable']);
        return response()->json(compact('maintenance'));
    }

    public function equipment(int $id): JsonResponse
    {
        $maintenances = Equipment::find($id)->maintenances;
        return response()->json(compact('maintenances'));
    }

    /**
     * @throws Throwable
     */
    public function copyMaintenance($maintainableType, $maintainableId) {
        try {
            DB::beginTransaction();
            $maintenances = request()->input('maintenances');

            /** @var Equipment|EquipmentGroup $maintainable */
            $maintainable = app()->make($maintainableType)->get($maintainableId);

            $companyItems = Item::where('company_id', Auth::user()->company_id)->get();

            foreach ($maintenances as $maintenance) {
                $maintenance = Maintenance::findOrFail($maintenance['id'])->load('items');

                $maintenance = $maintenance->replicate(['store_id', 'maintainable_id', 'maintainable_type']);
                $maintenance->store_id = $maintainable->store_id;
                $maintenance->maintainable_id = $maintainable->id;
                $maintenance->maintainable_type = get_class($maintainable);

                foreach ($maintenance->items as $item) {
                    if ($companyItems->where('name', $item->name)->first()) {
                        $item = $companyItems->where('name', $item->name)->first();
                        $inventory = Inventory::where(['item_id' => $item->id, 'store_id' => $maintainable->store_id])
                            ->exists();
                        $createInventory = !$inventory;
                    } else {
                        $createInventory = true;
                        $item = $item->replicate(['company_id']);
                        $item->company_id = Auth::user()->company_id;
                        $item->save();
                    }

                    if ($createInventory) {
                        Inventory::create([
                            'store_id' => $maintainable->store_id,
                            'cost' => 0,
                            'supplier_id' => 0,
                            'current_stock' => 0,
                            'initial_stock' => 0,
                            'desired_stock' => 0,
                        ]);
                    }
                }

                $maintenance->save();
            }
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollback();
            throw $throwable;
        }
    }
}
