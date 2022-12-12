<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\CompletedProcedureStep;
use App\Models\ProcedureAssignment;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class CompletedProcedureStepsController extends Controller
{
    public function index($storeId): JsonResponse
    {
        $assignments = ProcedureAssignment::where('assignment_id', '=', Auth::id())
            ->where('date', '=', Carbon::now()->format('Y-m-d'))
            ->whereHas('procedure', function (Builder $query) use ($storeId) {
                $query->where('procedures.store_id', $storeId);
            })
            ->with('procedure.steps')
            ->get();

        $completedSteps = CompletedProcedureStep::where('date', '=', Carbon::now()->format('Y-m-d'))->get();
        $procedures = $assignments->map(function (ProcedureAssignment $assignment) use ($completedSteps) {
            $procedure = $assignment->procedure;
            $procedure->assignment = $assignment->withoutRelations();
            $procedure->assignment->load('updated_by');
            foreach ($procedure->steps as $step) {
                $step->completed = $completedSteps->firstWhere('step_id', '=', $step->id);
                if (!$step->completed) {
                    $step->completed = CompletedProcedureStep::create([
                        'date' => Carbon::now()->format('Y-m-d'),
                        'step_id' => $step->id,
                    ]);
                }
            }

            return $procedure;
        });

        return response()->json(compact('procedures'));
    }

    public function view($procedureId): JsonResponse
    {
        /** @var ProcedureAssignment $assignment */
        $assignment = ProcedureAssignment::where('assignment_id', '=', Auth::id())
            ->where('date', '=', Carbon::now()->format('Y-m-d'))
            ->where('procedure_id', '=', $procedureId)
            ->with('procedure.steps')
            ->first();

        $completedSteps = CompletedProcedureStep::where('date', '=', Carbon::now()->format('Y-m-d'))->with('completed_by')->get();
        $procedure = $assignment->procedure;
        foreach ($procedure->steps as $step) {
            $step->completed = $completedSteps->firstWhere('step_id', '=', $step->id);
            if (!$step->completed) {
                $step->completed = CompletedProcedureStep::create([
                    'date' => Carbon::now()->format('Y-m-d'),
                    'step_id' => $step->id,
                ]);
            }
        }

        return response()->json(compact('procedure'));
    }

    public function toggle($id): JsonResponse
    {
        $complete = CompletedProcedureStep::findOrFail($id);

        if (request('complete')) {
            $complete->completed = true;
            $complete->completed_by_id = Auth::id();
            $complete->completed_at = Carbon::now();
        } else {
            $complete->completed = false;
            $complete->completed_by_id = null;
            $complete->completed_at = null;
        }
        $complete->save();
        $complete->load('completed_by');

        return response()->json(compact('complete'));
    }

    public function addNote($id): JsonResponse
    {
        $complete = CompletedProcedureStep::findOrFail($id);
        $complete->note = request('note');
        $complete->save();
        return response()->json(compact('complete'));
    }
}
