<?php

namespace App\Models;

use App\Traits\HasUniqueIdentifier;
use Auth;
use Carbon\Carbon;
use ChargeBee\ChargeBee\Models\Subscription;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Store
 *
 * @property int $id
 * @property string $name
 * @property int $number
 * @property int $file_id
 * @property int $company_id
 * @property string $address
 * @property string $state
 * @property string $country
 * @property int $zipcode
 * @property string $city
 * @property string $subscription_id
 * @property string $cancel_date
 * @property int $canceled
 * @property string $cancel_reason
 * @property string $setup_id
 * @property string $plan_id
 * @property int $store_type_id
 * @property int $current_car_count
 * @property int $allow_car_counts
 * @property int $maintenance_due_days_offset
 * @property int $maintenance_due_cars_offset
 * @property int $upcoming_days_offset
 * @property int $upcoming_cars_offset
 * @property string $time_zone
 * @property int $require_scan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|CarCount[] $car_counts
 * @property-read int|null $car_counts_count
 * @property-read Company|null $company
 * @property-read Collection|Equipment[] $equipments
 * @property-read int|null $equipments_count
 * @property-read File|null $files
 * @property-read Collection|Inventory[] $inventories
 * @property-read int|null $inventories_count
 * @property-read Collection|Location[] $locations
 * @property-read int|null $locations_count
 * @property-read Collection|OrderItem[] $order_items
 * @property-read int|null $order_items_count
 * @property-read Collection|Repair[] $repairs
 * @property-read int|null $repairs_count
 * @property-read Collection|Supplier[] $suppliers
 * @property-read int|null $suppliers_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Store newModelQuery()
 * @method static Builder|Store newQuery()
 * @method static Builder|Store query()
 * @method static Builder|Store settings()
 * @method static Builder|Store storesUsers(array $store_ids)
 * @method static Builder|Store usersStores(int $user_id)
 * @method static Builder|Store whereAddress($value)
 * @method static Builder|Store whereAllowCarCounts($value)
 * @method static Builder|Store whereCancelDate($value)
 * @method static Builder|Store whereCancelReason($value)
 * @method static Builder|Store whereCanceled($value)
 * @method static Builder|Store whereCity($value)
 * @method static Builder|Store whereCompanyId($value)
 * @method static Builder|Store whereCountry($value)
 * @method static Builder|Store whereCreatedAt($value)
 * @method static Builder|Store whereCurrentCarCount($value)
 * @method static Builder|Store whereFileId($value)
 * @method static Builder|Store whereId($value)
 * @method static Builder|Store whereMaintenanceDueCarsOffset($value)
 * @method static Builder|Store whereMaintenanceDueDaysOffset($value)
 * @method static Builder|Store whereName($value)
 * @method static Builder|Store whereNumber($value)
 * @method static Builder|Store wherePlanId($value)
 * @method static Builder|Store whereRequireScan($value)
 * @method static Builder|Store whereSetupId($value)
 * @method static Builder|Store whereState($value)
 * @method static Builder|Store whereStoreTypeId($value)
 * @method static Builder|Store whereSubscriptionId($value)
 * @method static Builder|Store whereTimeZone($value)
 * @method static Builder|Store whereUpcomingCarsOffset($value)
 * @method static Builder|Store whereUpcomingDaysOffset($value)
 * @method static Builder|Store whereUpdatedAt($value)
 * @method static Builder|Store whereZipcode($value)
 * @mixin Eloquent
 * @property-read \App\Models\File|null $image
 * @property int $skip_supply_gathering
 * @method static Builder|Store whereSkipSupplyGathering($value)
 * @property-read \App\Models\IncidentForm|null $incident_form
 * @property-read Collection|\App\Models\Maintenance[] $maintenances
 * @property-read int|null $maintenances_count
 * @property-read Collection|\App\Models\Procedure[] $procedures
 * @property-read int|null $procedures_count
 */
class Store extends Model
{
    use HasFactory;
    use HasUniqueIdentifier;

    public const TYPE_STORE = 1;
    public const TYPE_WAREHOUSE = 2;


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('active', function (Builder $query) {
            return $query->where(function (Builder $query) {
                $query->orWhere('stores.canceled', false);
                $query->orWhere('stores.cancel_date', Carbon::now()->subDays(7));
            });
        });

        static::creating(function (Store $store) {
            if ($store->store_type_id !== 1) {
                return;
            }

            $store->fill([
                'allow_car_counts' => $store->company->allow_car_count,
                'maintenance_due_days_offset' => 0,
                'maintenance_due_cars_offset' => 0,
                'upcoming_days_offset' => 7,
                'upcoming_cars_offset' => 4000,
                'require_scan' => false,
            ]);
        });

        static::created(function (Store $store) {
            if ($store->store_type_id !== 1) {
                return;
            }

            // Add Default Location
            $store->locations()->create([
                'name' => 'Tunnel',
                'description' => 'Default Location',
                'default_location' => true,
            ]);

            if (!app()->runningInConsole()) {
                $result = Subscription::createForCustomer($store->company->chargebee_customer_id, [
                    'planId' => $store->plan_id,
                ]);

                $subscription = $result->subscription();
                $store->subscription_id = $subscription->id;
                $store->save();
            }
        });
    }


    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function equipments(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function car_counts(): HasMany
    {
        return $this->hasMany(CarCount::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function incident_form(): HasOne
    {
        return $this->hasOne(IncidentForm::class);
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(Procedure::class);
    }

    public function scopeUsersStores(Builder $query, int $user_id) {
        return $query->whereHas('users', function (Builder $query) use ($user_id) {
           return $query->where('users.id', $user_id);
        });
    }

    public function scopeStoresUsers(Builder $query, array $store_ids) {
        return $query->whereIn('stores.id', $store_ids)->with('users', function (Builder $query) {
            return $query->select('id', DB::raw('CONCAT(users.first_name, " ", users.last_name as name'), 'role');
        });
    }

    public function scopeSettings(Builder $query) {
        return $query
            ->with([
                'location.equipments',
                'users.stores',
                'company',
                'suppliers.file',
                'file'
            ])
            ->where('stores.id', Auth::user()->active_store_id);
    }

}
