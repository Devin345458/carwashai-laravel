<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    public function getStoresLocations($storeId): JsonResponse
    {
        $locations = Location::storeLocations($storeId)->get();
        return response()->json(compact('locations'));
    }

    public function upsert($storeId) {
        if (request('id')) {
            $location = Location::find(request('id'));
        } else {
            $location = new Location([
                'store_id' => $storeId
            ]);
        }

        $location->fill(request()->input());
        $location->save();
    }

    public function reorder() {
        Location::setNewOrder(request()->input('locationIds'));
    }

    public function delete($locationId)
    {
        Location::find($locationId)->delete();
    }
}
