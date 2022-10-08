<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\TransferRequest;
use Auth;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class TransferRequestsController extends Controller
{
    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     * @throws Throwable
     */
    public function add(): void
    {
        $transferRequests = collect(request()->input())->map(fn($data) => new TransferRequest($data));

        $orderItems = OrderItem::whereIn('id', $transferRequests->pluck('order_item_id'))->get();
        $orderItems->each(function (OrderItem $orderItem) { $orderItem->order_item_status_id =  OrderItem::TRANSFER_REQUESTED; });

        DB::beginTransaction();
        try {
            TransferRequest::saveMany($transferRequests);
            OrderItem::saveMany($orderItems);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function getItemCounts(string $storeId = null): JsonResponse
    {
        return response()->json([
            'pendingCount' => TransferRequest::itemCount(TransferRequest::TRANSFER_REQUEST, Auth::id(), $storeId),
            'pickupCount' => TransferRequest::itemCount(TransferRequest::TRANSFER_APPROVED_FOR_PICKUP, Auth::id(), $storeId),
            'deliveryCount' => TransferRequest::itemCount(TransferRequest::TRANSFER_APPROVED_FOR_DELIVERY, Auth::id(), $storeId),
        ]);
    }

    /**
     * @throws Exception
     */
    public function getTransfersByStatus(string $storeId = null): JsonResponse
    {
        $transfers = TransferRequest::getTransfersByStatus(request('statusIds'), Auth::id(), $storeId)->get();
        return response()->json(compact('transfers'));
    }

    public function history(): JsonResponse
    {
        $transfers = TransferRequest::whereTransferStatusId(6)
            ->with([
                'order_item.inventory.item',
                'from_store',
                'to_store',
                'approved_by',
            ]);

        if (!Auth::user()->active_store_id) {
            $transfers->whereIn('from_store_id', Auth::user()->stores()->pluck('id')->toArray());
        } else {
            $transfers->where('from_store_id', Auth::user()->active_store_id);
        }

        $transfers = $transfers->get();

        return response()->json(compact('transfers'));
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function updateStatus()
    {
        $transfers = TransferRequest::whereIn('id', request('transfer_ids'))
            ->with(['order_item'])
            ->get()
            ->map(function (TransferRequest $transfer) {
                $transfer->transfer_status_id = request('transfer_status_id');
                $transfer->approved_by_id = Auth::id();
                if ($transfer->transfer_status_id === TransferRequest::TRANSFER_DENIED) {
                    $transfer->denial_reason = request('denial_reason');
                    $transfer->order_item->order_item_status_id = OrderItem::ACCEPTED;
                } else {
                    $transfer->order_item->expected_delivery_date = request('expected_delivery_date');
                }

                return $transfer;
            })->all();

        TransferRequest::pushMany($transfers);
    }

    /**
     * Delete method
     *
     * @param  int $id Transfer Request id.
     */
    public function delete(int $id)
    {
        TransferRequest::find($id)->delete();
    }
}
