<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Repair;
use Auth;
use BeyondCode\Comments\Comment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use Throwable;

class RepairsController extends Controller
{
    /**
     * Index method
     *
     * @param string|null $storeId The store to get repairs for
     */
    public function index(?string $storeId = null): JsonResponse
    {
        $filters = request('filters');
        $filters['search'] = request('search');
        $repairs = Repair::repairsFiltered(...$filters)
            ->activeStore($storeId)
            ->when(request('pagination.sort'), function (Builder $query, string $sort) {

                return $query->orderBy($sort, request('pagination.direction', 'desc'));
            })
            ->paginate(request('pagination.perPage'), ['*'], 'page', request('pagination.page'));

        return response()->json($repairs);
    }

    public function add(): JsonResponse
    {
        $data = request()->input();
        $data['status'] = Repair::STATUS_PENDING;
        if (isset($data['assigned_to_id'])) {
            $data['assigned_by_id'] = Auth::id();
            $data['assigned_date'] = new Carbon();
        }
        if (isset($data['findable_type'])) {
            $data['findable_type'] = 'App\\Models\\' . $data['findable_type'];
        }
        $repair = Repair::create($data);
        if (count(request('files'))) {
            $repair->files()->attach(collect(request('files'))->map(fn($repair) => $repair['id'])->toArray());
        }
        if (request('reminder')) {
            $repair->repair_reminder()->create([
                'user_id' => Auth::id(),
                'reminder' => new Carbon(request('reminder')),
                'sent' => 0
            ]);
        }
        $repair = Repair::repairsFiltered()->whereId($repair->id)->firstOrFail();
        return response()->json(compact('repair'));
    }

    public function view($id)
    {
        $repair = Repair::find($id)->load([
            'items.active_store_inventory',
            'files',
            'created_by',
            'findable.maintenance'
        ]);
        return response()->json(compact('repair'));
    }

    /**
     * Update a field on a repair
     *
     * @param int $id The id of the repair to update e
     */
    public function updateField(int $id): JsonResponse
    {
        $repair = Repair::find($id)->load([
            'files',
            'items.active_store_inventory',
        ]);

        $field = request('field');
        $value = request('value');

        if ($field === 'due_date') {
            $value = new Carbon($value);
        }

        $repair[$field] = $value;
        $repair->save();

        $repair = Repair::repairsTable()->whereId($repair->id)->firstOrFail();

        return response()->json(compact('repair'));
    }

    public function delete($repairId)
    {
        Repair::find($repairId)->delete();
    }

    public function completeRepair($id): JsonResponse
    {
        $repair = Repair::find($id);

        $data = request();

        if ($repair->completed) {
            $repair->completed = 0;
            $repair->status = $repair->assigned_by_id ? 'Assigned' : 'Pending Assignment';
            $repair->completed_reason = null;
        } else {
            $repair->completed = 1;
            $repair->status = 'Complete';
            $repair->completed_reason = $data['reason'];
        }

        $repair = Repair::repairsTable()->whereId($repair->id)->firstOrFail();

        return response()->json(compact('repair'));
    }

    public function dashboardWidget(?string $storeId = null): JsonResponse
    {
        $date = new Carbon();
        $upcoming_date = new Carbon('+7 days');
        $due = Repair::repairsFiltered()->activeStore($storeId)->where('due_date', '=', $date)->get();
        $overdue = Repair::dashboard()->activeStore($storeId)->where('due_date', '<', $date)->get();
        $upcoming = Repair::dashboard()->activeStore($storeId)->where([
            ['due_date', '>', $date],
            ['due_date', '<', $upcoming_date]
        ])->get();

        return response()->json(compact('due', 'overdue', 'upcoming'));
    }

    /**
     * Retrieve all activities on the equipment
     *
     * @param int $id The id of the equipment to get activities for
     */
    public function activities(int $id): JsonResponse
    {
        $repair = Repair::find($id);

        $activityLogs = Activity::query()
            ->orWhere([
                'subject_type' => Repair::class,
                'subject_id' => $repair->id
            ])
            ->orWhere([
                'subject_type' => Comment::class,
                ['subject_id', 'IN', $repair->comments()->pluck('id')->toArray()]
            ])
            ->with('causer')
            ->latest();

        $activityLogs = $activityLogs->paginate();

        return response()->json($activityLogs);
    }

    public function deleteItemFromRepair($id, $itemId)
    {
        $repair = Repair::find($id);
        $repair->items()->detach([$itemId]);
    }

    /**
     * @throws Throwable
     */
    public function bulkImport()
    {
        $repairs = collect(request());
        $repairs = $repairs->map(function ($field) {
            foreach ($field as $header => $value) {
                if (in_array($header, ['created', 'due_date'])) {
                    $field[$header] = new Carbon($value);
                }
            }

            return $field;
        })->toArray();
        Repair::createMany($repairs);
    }
}
