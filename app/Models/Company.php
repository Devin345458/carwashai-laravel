<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * App\Models\Company
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $city
 * @property string $state
 * @property int $zipcode
 * @property string $country
 * @property string $email
 * @property string $billing_last_name
 * @property string $billing_first_name
 * @property string $chargebee_customer_id
 * @property int $allow_car_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Category[] $categories
 * @property-read int|null $categories_count
 * @property-read Collection|Equipment[] $equipments
 * @property-read int|null $equipments_count
 * @property-read Collection|Store[] $stores
 * @property-read int|null $stores_count
 * @property-read Collection|Store[] $stores_and_warehouses
 * @property-read int|null $stores_and_warehouses_count
 * @property-read Collection|Supplier[] $suppliers
 * @property-read int|null $suppliers_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @property-read Collection|Store[] $warehouses
 * @property-read int|null $warehouses_count
 * @method static Builder|Company newModelQuery()
 * @method static Builder|Company newQuery()
 * @method static Builder|Company query()
 * @method static Builder|Company whereAddress($value)
 * @method static Builder|Company whereAllowCarCount($value)
 * @method static Builder|Company whereBillingFirstName($value)
 * @method static Builder|Company whereBillingLastName($value)
 * @method static Builder|Company whereChargebeeCustomerId($value)
 * @method static Builder|Company whereCity($value)
 * @method static Builder|Company whereCountry($value)
 * @method static Builder|Company whereCreatedAt($value)
 * @method static Builder|Company whereEmail($value)
 * @method static Builder|Company whereId($value)
 * @method static Builder|Company whereName($value)
 * @method static Builder|Company whereState($value)
 * @method static Builder|Company whereUpdatedAt($value)
 * @method static Builder|Company whereZipcode($value)
 * @mixin Eloquent
 */
class Company extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
    }

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class)
            ->where('store_type_id', '=', Store::TYPE_STORE);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Store::class)
            ->where('store_type_id', '=', Store::TYPE_WAREHOUSE);
    }

    public function stores_and_warehouses(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function equipments(): HasManyThrough
    {
        return $this->hasManyThrough(Equipment::class, Store::class);
    }
}
