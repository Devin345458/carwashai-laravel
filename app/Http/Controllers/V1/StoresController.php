<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Auth;
use ChargeBee\ChargeBee\Models\Subscription;
use DB;
use Exception;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Throwable;

class StoresController extends Controller
{
    /**
     * Return the users stores
     *
     * @param bool $pagination Whether to return all or paginate the request
     */
    public function getUsersStores(bool $pagination = true): JsonResponse
    {
        $stores =  Auth::user()->stores()->whereIn('store_type_id', request('store-type-ids', [1, 2]))
            ->withCount(['users']);

        if ($pagination) {
            $stores = $stores->paginate();
            return response()->json($stores);
        } else {
            $stores = $stores->get();
            return response()->json(compact('stores'));
        }
    }

    /**
     * Changes the users active store
     *
     * @param string|null $storeId
     * @return JsonResponse
     */
    public function setStore(?string $storeId = null): JsonResponse
    {
        $user = Auth::user();
        if ($storeId === null || $storeId === '0') {
            $user->active_store_id = null;
            $store = 0;
        } else {
            DB::enableQueryLog();
            $store = Store::find($storeId);
            $test = DB::getQueryLog();
            $user->active_store_id = $store->id;
        }
        $user->save();

        return response()->json(compact('store', 'user'));
    }

    public function settings()
    {
        $store = Store::settings()->first();
        return response()->json(compact('store'));
    }

    public function saveSettings(string $storeId): JsonResponse
    {
        $store = Store::find($storeId);
        $store->fill(request()->input());
        $store->save();

        return response()->json(compact('store'));
    }

    /**
     * Return the users for the store
     *
     * @param string $storeId The store id to get users for
     */
    public function users(string $storeId): JsonResponse
    {
        $users = Store::find($storeId)->users;
        return response()->json(compact('users'));
    }

    public function subscriptionEndPoint()
    {
        $data = request()->input();
        if ($data['event_type'] === 'subscription_cancellation_scheduled' || $data['event_type'] === 'subscription_deleted' || $data['event_type'] === 'subscription_cancelled') {
            /** @var Store $store */
            $store = Store::where(['subscription_id' => $data['content']['subscription']['id']])->firstOrFail();
            $store->canceled = true;
            $store->cancel_date = new Carbon($data['content']['subscription']['current_term_end']);
            $store->cancel_reason = $data['content']['subscription']['cancel_reason'] ?: 'Initiated By User';
            $store->save();
        } elseif ($data['event_type'] === 'subscription_scheduled_cancellation_removed' || $data['event_type'] === 'subscription_renewed') {
            /** @var Store $store  */
            $store = Store::where(['subscription_id' => $data['content']['subscription']['id']])->firstOrFail();
            $store->canceled = false;
            $store->cancel_date = null;
            $store->cancel_reason = null;
            $store->save();
        }
    }

    /**
     * @param string $store_id
     * @throws Exception
     */
    public function reactivate(string $storeId)
    {
        $store = Store::find($storeId);
        $result = Subscription::reactivate($store->subscription_id, [
            'invoiceImmediately' => true,
        ]);
        $subscription = $result->subscription();
        if (!$subscription) {
            throw new Exception('Unable to reactivate subscription, please contact support', 500);
        }

        $store->canceled = false;
        $store->cancel_date = null;
        $store->cancel_reason = null;
        $store->save();
    }

    public function delete(string $storeId)
    {
        Store::find($storeId)->delete();
    }

    /**
     * @param $storeId
     * @throws Exception
     */
    public function cancelSubscription($storeId)
    {
        $store = Store::find($storeId);
        $result = Subscription::cancel($store->subscription_id, [
            'endOfTerm' => true,
        ]);
        $subscription = $result->subscription();
        if (!$subscription) {
            throw new Exception('Unable to cancel subscription, please contact support', 500);
        }

        $store->canceled = true;
        $store->cancel_date = new Carbon($subscription->currentTermEnd);
        $store->cancel_reason = 'Initiated By User';
        $store->save();
    }

    /**
     * Add method
     * @throws Throwable
     */
    public function add(): void
    {
        DB::beginTransaction();
        try {
            $users = User::orWhere(['company_id' => Auth::user()->company_id, 'role' => 'owner'])
                ->orWhereIn('id', collect(request()->input('users'))->pluck('id')->toArray())
                ->select('id')
                ->pluck('id')
                ->toArray();


            $data = request()->except('users');
            $data['company_id'] = Auth::user()->company_id;
            if ($data['store_type_id'] === 2) {
                $data['plan_id'] = '0';
            }
            $store = Store::create($data);
            $store->users()->sync($users);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function view(Store $store): JsonResponse
    {
        return response()->json(compact('store'));
    }
}
