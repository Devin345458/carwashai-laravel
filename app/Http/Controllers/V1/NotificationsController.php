<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['notifications' => Auth::user()->notifications]);
    }

    public function read(int $id) {
        /** @var DatabaseNotification $notification */
        $notification = Auth::user()->notifications()->find($id);
        $notification->markAsRead();
    }
}
