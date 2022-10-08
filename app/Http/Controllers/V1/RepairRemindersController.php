<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\RepairReminder;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RepairRemindersController extends Controller
{
    /**
     * Upsert method
     */
    public function upsert()
    {
        $reminder = RepairReminder::firstOrNew([
            'repair_id' => request('id'),
            'user_id' => Auth::id()
        ]);
        $reminder->reminder = new Carbon(request('reminder'));
        $reminder->sent = false;
        $reminder->save();

        return response()->json(compact('reminder'));
    }

    public function getReminder($id): JsonResponse
    {
        $reminder = RepairReminder::where([
            'repair_id' => $id,
            'user_id' => Auth::id()
        ])->first();

        return response()->json(compact('reminder'));
    }
}
