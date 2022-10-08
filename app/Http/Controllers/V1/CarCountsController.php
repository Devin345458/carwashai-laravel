<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\CarCount;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarCountsController extends Controller
{
    public function checkCarCount(Request $request): JsonResponse
    {
        if (!Auth::user()->active_store) {
            return response()->json(['status' => false]);
        }

        if (!Auth::user()->active_store->allow_car_counts) {
            return response()->json(['status' => false]);
        }

        /** @var CarCount|null $car_counts */
        $car_counts = Auth::user()->active_store->car_counts()->latest()->first();
        if (!$car_counts) {
            return response()->json(['status' => 'Never Set']);
        }

        $today = Carbon::now()->setTimezone($request->input('timezone'));
        if ($car_counts->created_at->isSameDay($today)) {
            return response()->json(['status' => false]);
        }

        return response()->json(['status' => $car_counts->created_at]);
    }
}
