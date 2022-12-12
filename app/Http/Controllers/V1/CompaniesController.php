<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Auth;
use ChargeBee\ChargeBee\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psy\Util\Json;

class CompaniesController extends Controller
{
    public function getCompany(): JsonResponse
    {
        return response()->json(['company' => Auth::user()->company]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $company = Auth::user()
            ->company
            ->fill($request->input());
        $company->save();

        $data = [
            'firstName' => $company->billing_first_name,
            'lastName' => $company->billing_last_name,
            'email' => $company->email,
            'billingAddress' => [
                'firstName' => $company->billing_first_name,
                'lastName' => $company->billing_last_name,
                'line1' => $company->address,
                'city' => $company->city,
                'state' => $company->state,
                'zip' => $company->zipcode,
                'country' => $company->country,
            ],
        ];
        if ($request->input('token')) {
            $data['card'] = [
                'gateway' => 'stripe',
                'tmpToken' => $request->input('token'),
            ];
        }

        Customer::updateBillingInfo($company->chargebee_customer_id, $data);
        return response()->json(compact('company'));
    }

    public function storeCount(): JsonResponse
    {
        $count = Auth::user()->company->stores->count();

        return response()->json(compact('count'));
    }


    public function stores(): JsonResponse
    {
        return response()->json(Auth::user()->company->stores_and_warehouses()->paginate());
    }

    public function all(): JsonResponse
    {
        return response()->json(['stores' => Auth::user()->company->stores_and_warehouses()->get()]);
    }
}
