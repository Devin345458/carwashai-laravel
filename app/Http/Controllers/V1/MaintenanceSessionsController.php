<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\MaintenanceMaintenanceSession;
use App\Models\MaintenanceSession;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class MaintenanceSessionsController extends Controller
{
    /**
     * Get Active User Sessions
     *
     * @param string|null $storeId The store to check for an active maintenance session for the user
     * @return JsonResponse
     */
    public function getActiveUserSessionByStoreId(?string $storeId = null): JsonResponse
    {
        if (!$storeId) {
            return response()->json(['session' => null]);
        }

        $session = MaintenanceSession::where('created_by_id', Auth::id())
            ->where('store_id', $storeId)
            ->whereNull('end_time')
            ->with([
                'maintenances.tools.file',
                'maintenances.parts.file',
                'maintenances.consumables.file',
                'maintenances.maintainable',
                'maintenances.items'
            ])->first();

        return response()->json(compact('session'));
    }

    /**
     * @param string $storeId
     */
    public function markSkipped(string $storeId)
    {
        /** @var MaintenanceSession $session */
        $session = MaintenanceSession::where('created_by_id', Auth::id())
            ->where('store_id', $storeId)
            ->whereNull('end_time')->first();

        if (!$session) {
            return;
        }

        $session->end_time = new Carbon();
        $session->save();
    }

    /**
     * Complete the posted maintenances
     *
     * @param int $sessionId The session to complete maintenance for
     */
    public function completeMaintenance(int $sessionId)
    {
        $maintenance_ids = request()->input('maintenanceIds');
        MaintenanceMaintenanceSession::where('maintenance_session_id', $sessionId)
            ->whereIn('maintenance_id', $maintenance_ids)
            ->update(['complete' => true]);
    }

    /**
     * Add method
     * @throws Throwable
     */
    public function add(): JsonResponse
    {
        try {
            DB::beginTransaction();
            $session = MaintenanceSession::create([
                'store_id' => request('store_id'),
                'start_time' => new Carbon()
            ]);
            $session->maintenances()->attach(request('maintenance_ids'));
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json(compact('session'));
    }

    /**
     * Complete Session method
     *
     * @throws Exception
     */
    public function completeMaintenanceSession(): JsonResponse
    {
        $data = request()->input();
        $session = MaintenanceSession::find($data['session_id']);
        $session->end_time = new Carbon();
        foreach ($data['items_used'] as $item_used) {
            $item = Item::find($item_used['id']);
            Inventory::use($item, (int)$item_used['quantity'], $session->store_id, 5);
        }
        $session->save();
        return response()->json(['success' => true]);
    }

    public function dashboardWidget(): JsonResponse
    {
        return response()->json(['maintenance_cost' => 1183]);
    }
}
