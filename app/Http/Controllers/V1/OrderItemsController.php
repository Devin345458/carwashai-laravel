<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\TransferRequest;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class OrderItemsController extends Controller
{
    /**
     * @throws Throwable
     */
    public function updateStatus()
    {
        $data = collect(request()->input());
        $orderItems = OrderItem::whereIn('id', $data->pluck('id'))->get();

        foreach ($orderItems as $orderItem) {
            $orderItem->order_item_status_id =  $data->firstWhere('id', $orderItem->id)['status'];
        }

        OrderItem::saveMany($orderItems);
    }

    public function approvedOrderItems(string $storeId = null): JsonResponse
    {
        $items = OrderItem::activeStore($storeId)->where('order_item_status_id', 3)
            ->with([
                'store',
                'inventory.item',
                'inventory.supplier'
            ])
            ->get();

        return response()->json(compact('items'));
    }

    public function purchaseOrderItems(string $storeId = null): JsonResponse
    {
        $ids = request()->input('ids') ?: [4, 6];
        $items = OrderItem::activeStore($storeId)->whereIn('order_item_status_id', $ids)
            ->with([
                'store',
                'inventory.item',
                'inventory.supplier',
                'active_transfer'
            ])
            ->get();

        return response()->json(compact('items'));
    }

    public function pendingDelivery(string $storeId = null): JsonResponse
    {
        $items = OrderItem::activeStore($storeId)
            ->whereIn('order_item_status_id', [OrderItem::ORDERED, OrderItem::TRANSFER_REQUESTED])
            ->with([
                'store',
                'inventory.item',
                'inventory.supplier',
                'active_transfer'
            ]);

        if (request()->input('late')) {
            $items->where('expected_delivery_date', '<', Carbon::now());
        } else {
            $items->where('expected_delivery_date', '>=', Carbon::now());
        }

        $items = $items->get();

        return response()->json(compact('items'));
    }

    public function history($storeId = null)
    {
        $items = OrderItem::activeStore($storeId)
            ->whereIn('order_item_status_id', [OrderItem::RECEIVED, OrderItem::DENIED])
            ->with([
                'store',
                'inventory.item',
                'received_by'
            ])
            ->orderByDesc('actual_delivery_date')
            ->paginate();

        return response()->json($items);
    }

    public function create($storeId): JsonResponse
    {
        OrderItem::create([
            'order_item_status_id' => OrderItem::PENDING,
            'store_id' => $storeId,
            'inventory_id' => request('inventory_id'),
            'quantity' => request('quantity'),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * @throws Throwable
     */
    public function markOrdered()
    {
        $orderItems = collect(request()->input())
            ->map(function ($orderItemData) {
                $orderItem = OrderItem::find($orderItemData['id']);
                $orderItem->fill($orderItemData);
                return $orderItem;
            });

        OrderItem::saveMany($orderItems);
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function markReceived()
    {
        $data = request()->input();
        $orderItems = OrderItem::whereIn('id', collect($data)->pluck('id')->toArray())
            ->with(['active_transfer'])
            ->get();

        $orderItems = $orderItems->map(function (OrderItem $orderItem, $index) use ($data) {
            if ($orderItem->active_transfer) {
                $orderItem->active_transfer->transfer_status_id = TransferRequest::TRANSFER_COMPLETED;
            }
            $orderItem->order_item_status_id = OrderItem::RECEIVED;
            $orderItem->received_by_id = Auth::id();
            $orderItem->receiving_comment = $data[$index]['receiving_comment'];
            $orderItem->location = $data[$index]['location'];
            $orderItem->actual_delivery_date = new Carbon();

            return $orderItem;
        });

        OrderItem::pushMany($orderItems);
    }

    public function pendingOrderItems($storeId = null): JsonResponse
    {
        $items = OrderItem::activeStore($storeId)
            ->where('order_item_status_id', OrderItem::PENDING)
            ->with([
                'store',
                'inventory.item',
                'created_by'
            ])->get();

        return response()->json(compact('items'));
    }

    public function getItemCounts($storeId = null)
    {
        return response()->json([
            'order_counts' => [
                'pendingCount' => OrderItem::itemCount([1], Auth::id(), $storeId),
                'acceptedCount' => OrderItem::itemCount([3], Auth::id(), $storeId),
                'purchaseCount' => OrderItem::itemCount([4, 6], Auth::id(),$storeId),
            ]
        ]);
    }

    public function orderItemsByInventory($id): JsonResponse
    {
        return response()->json(OrderItem::where('inventory_id', $id)->paginate());
    }

    public function dashboardWidget(string $storeId): JsonResponse
    {
        $date = new Carbon();
        $items = OrderItem::activeStore($storeId)
            ->where('expected_delivery_date', '<=', $date)
            ->whereNull('actual_delivery_date')
            ->with(['inventory.item'])
            ->get();

        return response()->json(compact('items'));
    }
}
