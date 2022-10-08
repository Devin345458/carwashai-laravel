<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\CompletedInventory;
use App\Models\InventoryTransaction;
use App\Models\OrderItem;
use App\Models\TransactionAction;
use DB;
use Illuminate\Http\JsonResponse;
use Throwable;

class InventoryTransactionsController extends Controller
{
    /**
     * Index method
     *
     * @param int $id
     * @return JsonResponse
     */
    public function inventoryItemHistory(int $id): JsonResponse
    {
        $inventoryTransactions = InventoryTransaction::where('inventory_id', $id)
            ->with('transaction_action', 'inventory.store', 'created_by', 'updated_by')
            ->latest()
            ->paginate();

        return response()->json($inventoryTransactions);
    }

    /**
     * @throws Throwable
     */
    public function saveConductInventory($storeId)
    {
        try {
            DB::beginTransaction();
            $data = request()->input();

            // Save the completed Inventory Record
            CompletedInventory::create([
                'time_to_complete' => $data['time'],
                'item_count' => count($data['completed_inventory']),
                'item_skip_count' => $data['skipped_inventory_count'],
                'store_id' => $storeId,
            ]);

            // Save the transactions
            $transactions = collect($data['completed_inventory'])->map( function ($inventory) {
                return InventoryTransaction::record(
                    $inventory['id'],
                    $inventory['actual_stock'],
                    TransactionAction::INVENTORY_CONDUCTED,
                    false
                );
            })->all();
            InventoryTransaction::saveMany($transactions);

            // Save the orders
            $items_to_order = collect($data['completed_inventory'])->filter(function ($inventory) {
                return $inventory['order'];
            });
            OrderItem::order($items_to_order, $storeId, 'Conduct Inventory');
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * History method
     */
    public function history(): JsonResponse
    {
        $data = response()->input();
        $store_ids = $data['store_ids'];
        $inventory_used = InventoryTransaction::whereIn('store_id', $store_ids)
            ->where(['created >' => $data['start_date'], 'created <' => $data['end_date']])
            ->with('inventory.item');

        return response()->json(
            [
                'report' => $inventory_used,
                'headers' => [
                    ['value' => 'inventories.item.name', 'text' => 'Name'],
                    ['value' => 'model', 'text' => 'Used During'],
                    ['value' => 'createdBy.full_name', 'text' => 'Used By'],
                    ['value' => 'created', 'text' => 'Used'],
                ],
                '_serialize' => ['report', 'headers'],
            ]
        );
    }
}
