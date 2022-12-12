<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Store;
use App\Models\TransactionAction;
use Auth;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoriesController extends Controller
{
    /**
     * Index method
     */
    public function index($storeId = null): JsonResponse
    {
        $inventories = Inventory::activeStore($storeId)
            ->with('item.item_type', 'store', 'supplier')
            ->when(request()->input('search'), function (Builder $query, $search) {
                $query->whereHas('item', function (Builder $query) use ($search) {
                    return $query->where('items.name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(request()->input('type_ids'), function (Builder $query, $typeIds) {
                $query->whereHas('item.item_type', function (Builder $query) use ($typeIds) {
                    return $query->whereIn('item_types.id', $typeIds);
                });
            });

        if (request('all')) {
            $inventories = $inventories->get();
            return response()->json(compact('inventories'));
        } else {
            return response()->json($inventories->paginate());
        }
    }

    /**
     * View method
     *
     * @param int $id Inventory id.
     */
    public function view(int $id): JsonResponse
    {
        $inventory = Inventory::whereId($id)
            ->with('item')
            ->firstOrFail();

        return response()->json(compact('inventory'));
    }

    /**
     * upsert method
     * @throws Exception
     */
    public function upsert(): JsonResponse
    {
        $message = 'Successfully saved inventory record';
        $data = request()->input();
        if (request('id')) {
            $inventory = Inventory::findOrFail(request('id'))->load('item');
            $inventory->fill($data);
            $inventory->item->fill($data['item']);
            if ($inventory->isDirty('current_stock')) {
                InventoryTransaction::record($inventory->id, $data['current_stock'], TransactionAction::INVENTORY_CONDUCTED);
            }
        } else {
            // Look to see if the item already exists in the database
            /** @var Item $itemLookUp */
            $itemLookUp = Item::where([
                [DB::raw('LOWER(name)'), '=', strtolower($data['item']['name'])],
                ['company_id', 'in', [1, Auth::user()->company_id]],
            ])->with('inventories')->first();

            // If we find an item check that if it has an inventory record for the store
            if ($itemLookUp) {
                $found = $itemLookUp->inventories->where('store_id', $data['store_id'])->first();
                if ($found) {
                    throw new Exception('This item already exists in your store', 422);
                }

                // Remove their item creation and set it to the existing item
                unset($data['item']);
                $message = 'There was already item in the catalogue with this name so we created you a inventory record for it';
                $data['item_id'] = $itemLookUp->id;
            }

            if (isset($data['item'])) {
                $item = Item::create($data['item']);
                $data['item_id'] = $item->id;
                unset($data['item']);
            }
            $inventory = new Inventory($data);
        }

        $inventory->push();

        $inventory->load('item');
        return response()->json(['successMessage' => $message, 'inventory' => $inventory]);
    }

    public function storesWithInventory($itemId, $quantity, $storeId = null): JsonResponse
    {
        $stores = Store::whereHas('inventories', function (Builder $query) use ($quantity, $itemId) {
                return $query
                    ->where('inventories.current_stock', '>=', $quantity,)
                    ->where('inventories.item_id', $itemId);
            })
            ->with([
                'inventories' => function (HasMany $query) use ($itemId) {
                    return $query->where('inventories.item_id', '=', $itemId);
                }
            ])
            ->where('stores.id', '<>', $storeId)
            ->where('stores.company_id', Auth::user()->company_id)
            ->get();

        return response()->json(compact('stores'));
    }

    public function types(): JsonResponse
    {
        $types = ItemType::whereIn('item_types.company_id', [Auth::user()->company_id, 1])->get();
        return response()->json(compact('types'));
    }

    public function dashboardWidget($storeId = null): JsonResponse
    {
        $total = Inventory::activeStore($storeId)->sum(DB::raw('current_stock * cost'));
        return response()->json(compact('total'));
    }

    public function createStoreRecord(): JsonResponse
    {
        $items = request()->input('items');
        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                Inventory::create($item['active_store_inventory']);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return response()->json(['success' => true]);
    }

}
