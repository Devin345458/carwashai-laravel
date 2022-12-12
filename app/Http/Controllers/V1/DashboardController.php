<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentGroup;
use App\Models\IncidentFormSubmission;
use App\Models\Maintenance;
use App\Models\OrderItem;
use App\Models\Repair;
use App\Models\Store;
use App\Models\User;
use Auth;
use BeyondCode\Comments\Comment;
use Carbon\Carbon;
use ChargeBee\ChargeBee\Models\Order;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function recommendations(Store $store): JsonResponse
    {
        $recommendations = [
            'equipment' => false,
            'maintenance' => false,
            'inventory' => false,
            'todo' => false,
            'incident' => false,
            'progress' => false,
            'settings' => false,
            'procedure' => false
        ];

        if (Auth::user()->role === 'user') {
            return response()->json(compact('recommendations'));
        }

        $recommendations['equipment'] = !$store->equipments()->exists();
        $recommendations['maintenance'] = !$store->maintenances()->exists();
        $recommendations['inventory'] = !$store->inventories()->exists();
        $recommendations['todo'] = !$store->repairs()->exists();
        $recommendations['incident'] = !$store->incident_form()->exists() || $store->incident_form->current_version->version === 1;
        $recommendations['settings'] = $store->name === 'Store 1' || !$store->file_id;
        $recommendations['procedure'] = !$store->procedures()->exists();

        $progress = 0;
        foreach ($recommendations as $recommendation) {
            if (!$recommendation) {
                $progress += 100 / count($recommendations);
            }
        }

        $recommendations['progress'] = $progress;

        return response()->json(compact('recommendations'));
    }

    public function activity(Store $store = null): JsonResponse
    {
        DB::enableQueryLog();
        $activityLogs = Activity::query()
            ->with('causer')
            ->latest();

        if (!$store) {
            $activityLogs->whereIn('causer_id', User::where('company_id', Auth::user()->company_id)->pluck('users.id')->toArray());
        } else {
            $activityLogs->whereIn('causer_id', $store->users()->pluck('users.id')->toArray());
        }


        $activityLogs = $activityLogs->paginate();

        return response()->json($activityLogs);
    }

    public function statistics(string $store = null): JsonResponse
    {
        $tasks = Repair::activeStore($store)->where('due_date', '<=', Carbon::now())->count();
        $maintenances = Maintenance::due($store, true)->count();
        $incidentReports = IncidentFormSubmission::activeStore($store)->active()->count();
        $orders = OrderItem::activeStore($store)->where('order_item_status_id', OrderItem::PENDING)->count();


        return response()->json([
            'stats' => [
                'inventory' => [
                    'due' => false,
                    'inventoried' => 0,
                    'total' => 0
                ],
                'todo' => $tasks,
                'maintenance' => $maintenances,
                'incident_reports' => $incidentReports,
                'orders' => $orders
            ]
        ]);
    }
}
