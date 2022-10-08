<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function search(): JsonResponse
    {
        $items = Item::where('company_id', Auth::user()->company_id)
            ->when(request('search'), function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->orWhere('items.name', 'LIKE', '%' . $search . '%');
                    $query->orWhere('items.description', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(request('excludeInventory'), function (Builder $query, string $storeId) {
                $query->whereDoesntHave('inventories', function (Builder $query) use ($storeId) {
                    $query->where('inventories.store_id', $storeId);
                });
            })
            ->when(request()->input('type_id'), function (Builder $query, int $typeId) {
              return $query->where('item_type_id', $typeId);
            })
            ->latest()
            ->limit(10)
            ->with([ 'active_store_inventory', 'file'])
            ->get();

        if (request()->input('selected') && !$items->where('id', request()->input('selected'))->first()) {
            $items->push(Item::find(request()->input('selected'))->load(['active_store_inventory', 'file']));
        }

        return response()->json(compact('items'));
    }

    /**
     * Add method
     */
    public function upsert(): JsonResponse
    {
        $data = request()->input();
        if ($data['id']) {
            $item = Item::find($data['id'])->load('active_store_inventory');
        } else {
            $item = new Item;
        }
        $item->fill($data);
        $item->company_id = Auth::user()->company_id;
        $item->save();
        $item->load('file');
        return response()->json(compact('item'));
    }
}
