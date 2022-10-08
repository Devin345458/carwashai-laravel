<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceMaintenanceSession;
use App\Models\Store;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;

class MaintenanceSessionsMaintenancesController extends Controller
{
    /**
     * Adds a comment to a maintenance session
     *
     * @param int $id The maintenance session id
     */
    public function addComment(int $id): JsonResponse
    {
        $maintenance_session_maintenance = MaintenanceMaintenanceSession::find($id);

        $comment = $maintenance_session_maintenance->comment(request()->input('comment'));

        $comment->load('commentator.file');

        return response()->json(compact('comment'));
    }

    /**
     * Get comments to a maintenance session maintenance items
     *
     * @param int $id The maintenance session id
     */
    public function getComments(int $id): JsonResponse
    {
        $comments = MaintenanceMaintenanceSession::find($id)->comments;
        return response()->json(compact('comments'));
    }

    public function search(?string $storeId = null) {
        $query = MaintenanceMaintenanceSession::where('complete', 1)
            ->when(request('search'), function (Builder $query, $search) {
                $query->whereHas('maintenance', function (BelongsTo $query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                });
            })
            ->with(['maintenance'])
            ->limit(20);

        if ($storeId) {
            $query->whereHas('maintenance', function (BelongsTo $query) use ($storeId) {
                $query->where('store_id', $storeId);
            });
        } else {
            $query->whereHas('maintenance.store.users', function (Builder $query) {
                return $query->where('users.id', Auth::id());
            });
        }

        return response()->json(['maintenances' => $query->get()]);
    }
}
