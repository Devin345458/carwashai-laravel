<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Store;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;
use Throwable;

class CarWashAiCompanySeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            /** @var Company $company */
            $company = Company::create([
                'id' => 1,
                'name' => 'Car Wash Ai',
                'email' => 'hollistergraham123@gmail.com',
                'billing_last_name' => 'Hollister-Graham',
                'billing_first_name' => 'Devin',
                'chargebee_customer_id' => '169lpUT0lBoDN1GEp',
                'allow_car_count' => 0
            ]);



            /** @var User $user */
            $user = User::create([
                'email' => 'hollistergraham123@gmail.com',
                'password' => 'SQJb69X8',
                'first_name' => 'Car Wash',
                'last_name' => 'Ai',
                'active' => 1,
                'role' => 'owner',
                'active_store_id' => null,
                'file_id' => null,
                'company_id' => $company->id
            ]);

            Auth::setUser($user);

            /** @var Store $store */
            $store = Store::create([
                'name' => 'Car Wash Ai',
                'number' => null,
                'file_id' => null,
                'address' => null,
                'state' => null,
                'country' => null,
                'zipcode' => null,
                'city' => null,
                'subscription_id' => null,
                'cancel_date' => null,
                'canceled' => 0,
                'cancel_reason' => null,
                'setup_id' => null,
                'plan_id' => 'yearly',
                'store_type_id' => 1,
                'current_car_count' => 0,
                'allow_car_counts' => 0,
                'maintenance_due_days_offset' => 0,
                'maintenance_due_cars_offset' => 0,
                'upcoming_days_offset' => 7,
                'upcoming_cars_offset' => 4000,
                'time_zone' => 'American/Chicago',
                'require_scan' => 0,
                'company_id' => $company->id,
            ]);

            $store->users()->attach($user);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
