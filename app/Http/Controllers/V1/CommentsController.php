<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;

class CommentsController extends Controller
{
    public function index() {
        $model = app()->make('\App\Models\\' . request('commentable_type'));
        $model = $model->findOrFail(request('commentable_id'));
        $comments = $model->comments()->with(['commentator.profile_image'])->get();
        return response()->json(compact('comments'));
    }

    public function add() {
        $model = app()->make('\App\Models\\' . request('commentable_type'));
        $model = $model->findOrFail(request('commentable_id'));
        $comment = $model->comment(request('content'));
        $comment->load('commentator.profile_image');
        return response()->json(compact('comment'));
    }
}
