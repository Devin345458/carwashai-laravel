<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class SuppliersController extends Controller
{
    public function index(string $storeId = null): JsonResponse
    {
        $suppliers = Supplier::activeStore($storeId)
            ->when(request('search'), function (Builder $query, $search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            })
            ->paginate();

        return response()->json($suppliers);
    }

    public function upsert(string $storeId): JsonResponse
    {
        if (request('id')) {
            $supplier = Supplier::find(request('id'));
        } else {
            $supplier = new Supplier([
                'store_id' => $storeId
            ]);
        }

        $supplier->fill(request()->input());
        $supplier->save();

        return response()->json(compact('supplier'));
    }

    public function delete(int $supplierId): JsonResponse
    {
        Supplier::find($supplierId)->delete();
        return response()->json(['success' => true]);
    }

    public function search(string $storeId = null): JsonResponse
    {
        $suppliers = Supplier::activeStore($storeId)
            ->when(request('search'), function (Builder $query, $search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            })
            ->orWhere('id', request('selected'))
            ->get();

        return response()->json(compact('suppliers'));
    }
}
