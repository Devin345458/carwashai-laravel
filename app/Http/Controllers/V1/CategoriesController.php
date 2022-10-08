<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function getCompanyCategories($type = null): JsonResponse
    {
        $categories = Category::where('company_id', Auth::user()->company_id)
            ->when($type, function (Builder $query, $type) {
                return $query->where('type', $type);
            })->get();
        return response()->json(compact('categories'));
    }
}
