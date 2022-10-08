<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Procedure;
use App\Models\ProcedureAssignment;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ProcedureAssignmentsController extends Controller
{
    public function index(string $storeId): JsonResponse
    {
        $procedures = [];
        $procedures['today'] = Procedure::where('store_id', '=', $storeId)
            ->whereHas('days', function (Builder $query) {
                $query->where('procedure_days.day_of_week', '=', Carbon::now()->dayOfWeek);
            })->get();
        $procedures['tomorrow'] = Procedure::where('store_id', '=', $storeId)
            ->whereHas('days', function (Builder $query) {
                $query->where('procedure_days.day_of_week', '=', Carbon::tomorrow()->dayOfWeek);
            })->get();

        $todayAssignment = ProcedureAssignment::where('date', Carbon::now()->format('Y-m-d'))->get();
        $tomorrowAssignment = ProcedureAssignment::where('date', Carbon::tomorrow()->format('Y-m-d'))->get();


        foreach ($procedures['today'] as $procedure) {
            $procedure->assignment = $todayAssignment->firstWhere('procedure_id', '=', $procedure->id);
        }

        foreach ($procedures['tomorrow'] as $procedure) {
            $procedure->assignment = $tomorrowAssignment->firstWhere('procedure_id', '=', $procedure->id);
        }

        return response()->json(compact('procedures'));
    }

    public function assign(): JsonResponse
    {
        $procedureAssignment = ProcedureAssignment::where([
            'procedure_id' => request('procedureId'),
            'date' => Carbon::createFromTimestamp(request('date') / 1000)->format('Y-m-d')
        ])->first();
        if (!$procedureAssignment) {
            $procedureAssignment = new ProcedureAssignment([
                'procedure_id' => request('procedureId'),
                'date' => new Carbon(request('date') / 1000)
            ]);
        }
        $procedureAssignment->assignment_id = request('assignmentId');
        $procedureAssignment->save();

        return response()->json(compact('procedureAssignment'));
    }
}
