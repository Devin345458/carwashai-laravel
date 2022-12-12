<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;
use Throwable;

class DemoCompanySeeder extends Seeder
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
            $company = Company::firstOrCreate([
                'name' => 'Demo Company',
            ], [
                'email' => 'demo@carwashai.com',
                'billing_last_name' => 'Doe',
                'billing_first_name' => 'John',
                'chargebee_customer_id' => '169lpUT0lBoDN1GEp',
                'allow_car_count' => 0
            ]);

            Role::addDefaultRoles($company->id);


            /** @var User $user */
            $user = User::firstOrCreate([
                'email' => 'demo@carwashai.com',
                'password' => 'VQJbzwX9X8',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'active' => 1,
                'role' => 'owner',
                'active_store_id' => null,
                'file_id' => null,
                'company_id' => $company->id
            ]);

            Auth::setUser($user);

            $user->assignRole('Owner');

            /** @var Store $store */
            $store = Store::firstOrCreate([
                'name' => 'Demo Wash',
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
