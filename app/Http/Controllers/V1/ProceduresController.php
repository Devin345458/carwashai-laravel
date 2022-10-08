<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Procedure;
use App\Models\ProcedureDay;
use DB;
use Illuminate\Http\JsonResponse;
use Throwable;

class ProceduresController extends Controller
{
    public function index(string $storeId): JsonResponse
    {
        $procedures = Procedure::where('store_id', $storeId)
            ->withCount('steps')
            ->with('days')
            ->paginate();
        return response()->json($procedures);
    }

    public function view(Procedure $procedure): JsonResponse
    {
        $procedure->load(['steps', 'days']);
        return response()->json(compact('procedure'));
    }

    /**
     * @throws Throwable
     */
    public function upsert(string $storeId): JsonResponse
    {
        DB::beginTransaction();
        try {
            if (request('id')) {
                $procedure = Procedure::findOrFail(request('id'));
            } else {
                $procedure = new Procedure([
                    'store_id' => $storeId
                ]);
            }

            $procedure->name = request('name');
            $procedure->save();

            $procedure->steps()->delete();
            $procedure->days()->delete();

            $procedure->steps()->createMany(request('steps'));
            $procedure->days()->createMany(request('days'));

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json(compact('procedure'));
    }

    public function delete(Procedure $procedure): JsonResponse
    {
        $procedure->delete();
        return response()->json(['success' => true]);
    }
}
