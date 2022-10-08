<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\IncidentFormSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecordingsController extends Controller
{
    public function save($id): JsonResponse
    {
        $submission = IncidentFormSubmission::findOrFail($id);
        $submission->recordings()->delete();
        $submission->recordings()->createMany(request('recordings'));

        return response()->json(['recordings' => $submission->recordings()->get()]);
    }
}
