<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ItemType;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemTypesController extends Controller
{
    /**
     * Index method
     *
     */
    public function index(): JsonResponse
    {
        $itemTypes = ItemType::whereIn('company_id', [Auth::user()->company_id, 1])
            ->with([
                'company' => function (BelongsTo $query) {
                    return $query->select(['companies.id', 'companies.name']);
                }
            ])
            ->paginate();

        return response()->json($itemTypes);
    }

    /**
     * Add method
     */
    public function add(): JsonResponse
    {
        $itemType = new ItemType(request()->input());
        $itemType->company_id = Auth::user()->company_id;
        $itemType->save();

        return response()->json(compact('itemType'));
    }

    /**
     * Edit method
     *
     * @param int $id Item Type id.
     */
    public function edit(int $id)
    {
        $itemType = ItemType::find($id);
        $itemType->fill(request()->input());
        $itemType->save();
    }

    /**
     * Delete method
     *
     * @param int $id Item Type id.
     * @throws Exception
     */
    public function delete(int $id)
    {
        $itemType = ItemType::find($id);
        if ($itemType->items_count) {
            throw new Exception('You must delete all items associated with this item type before you can delete it');
        }
        $itemType->delete();
    }
}
