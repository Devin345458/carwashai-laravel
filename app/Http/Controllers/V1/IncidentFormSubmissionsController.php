<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\IncidentForm;
use App\Models\IncidentFormSubmission;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Spatie\Activitylog\Models\Activity;

class IncidentFormSubmissionsController extends Controller
{
    /**
     * Index method
     *
     */
    public function index(?string $storeId = null): JsonResponse
    {
        $incidentFormSubmissions = IncidentFormSubmission::activeStore($storeId)
            ->with(['user', 'store'])
            ->when(request()->input('status'), function (Builder $query, string $status) {
                $query->where('status', $status);
            })
            ->paginate();

        return response()->json($incidentFormSubmissions);
    }

    /**
     * View method
     *
     * @param int $id Incident Form Submission id.
     */
    public function view(int $id): JsonResponse
    {
        $incidentFormSubmission = IncidentFormSubmission::whereId($id)
            ->with('incident_form_version', 'user', 'recordings', 'contact_logs')
            ->firstOrFail();

        return response()->json(compact('incidentFormSubmission'));
    }

    /**
     * Add method
     */
    public function add(string $storeId): JsonResponse
    {
        $incidentFormSubmission = new IncidentFormSubmission(request()->input());
        $incidentFormSubmission->store_id = $storeId;
        $incidentFormSubmission->save();

        return response()->json(compact('incidentFormSubmission'));
    }

    /**
     * Edit method
     *
     * @param int $id Incident Form Submission id.
     */
    public function edit(int $id): JsonResponse
    {
        $incidentFormSubmission = IncidentFormSubmission::whereId($id)
            ->with('incident_form_version', 'user', 'recordings', 'contact_logs')
            ->firstOrFail();

        $incidentFormSubmission->fill(request()->input())->save();

        return response()->json(compact('incidentFormSubmission'));
    }

    public function changeStatus(int $id): JsonResponse
    {
        $incidentFormSubmission = IncidentFormSubmission::findOrFail($id);
        $incidentFormSubmission->status = request()->input('status');
        $incidentFormSubmission->save();

        return response()->json(['success' => true]);
    }

    /**
     * Retrieve all activities on the equipment
     *
     * @param int $id The id of the equipment to get activities for
     */
    public function activities(int $id): JsonResponse
    {
        $incidentFormSubmission = IncidentFormSubmission::findOrFail($id);
        DB::enableQueryLog();
        $activityLogs = Activity::query()
            ->orWhere([
                'subject_type' => IncidentFormSubmission::class,
                'subject_id' => $incidentFormSubmission->id
            ])
            ->orWhere(function (Builder $query) use ($incidentFormSubmission) {
                $query->where('subject_type',Comment::class);
                $query->whereIn('subject_id', $incidentFormSubmission->comments()->pluck('id')->toArray());
            })
            ->with('causer')
            ->latest()
            ->paginate();

        return response()->json($activityLogs);
    }

    public function metrics(?string $storeId = null): JsonResponse
    {
        $metrics = [
            'open_claims' => 0,
            'claims_this_month' => 0,
            'claims_by_status' => [
                'received' => 0,
                'reviewing' => 0,
                'contacting_client' => 0,
                'getting_quote' => 0,
                'denied' => 0,
                'accepted' => 0
            ]
        ];

        $incidentForms = IncidentForm::activeStore($storeId)->get();
        foreach ($incidentForms as $incidentForm) {
            $metrics['open_claims'] += $incidentForm->submissions()
                ->whereNotIn('status', [IncidentFormSubmission::STATUS_DENIED, IncidentFormSubmission::STATUS_ACCEPTED])
                ->count();

            $metrics['claims_this_month'] += $incidentForm->submissions()
                ->where('created_at', '<=', Carbon::now()->setDay(0))
                ->count();

            $statuses = $incidentForm->submissions()
                ->select(['status', DB::raw('COUNT(*) as total')])
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            foreach ($statuses as $status => $value) {
                $metrics['claims_by_status'][$status] += $value;
            }
        }

        return response()->json(compact('metrics'));
    }
}
