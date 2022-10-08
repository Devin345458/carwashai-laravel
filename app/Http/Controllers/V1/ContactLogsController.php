<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\IncidentFormSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactLogsController extends Controller
{
    public function save($id): JsonResponse
    {
        $submission = IncidentFormSubmission::findOrFail($id);
        $submission->contact_logs()->delete();
        $submission->contact_logs()->createMany(request('logs'));


        return response()->json(['contactLogs' => $submission->contact_logs()->get()]);
    }
}
