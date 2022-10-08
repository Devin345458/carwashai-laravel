<?php

namespace App\Models;

use App\Traits\ActiveStore;
use App\Traits\WhoDidIt;
use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Supplier
 *
 * @property int $id
 * @property string $name
 * @property string $website
 * @property string $phone
 * @property string $email
 * @property string $contact_name
 * @property string $store_id
 * @property int $file_id
 * @property int $supplier_type_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read Collection|Equipment[] $equipments
 * @property-read int|null $equipments_count
 * @property-read File|null $file
 * @property-read Collection|Inventory[] $inventories
 * @property-read int|null $inventories_count
 * @property-read Store|null $store
 * @method static Builder|Supplier companySuppliers()
 * @method static Builder|Supplier newModelQuery()
 * @method static Builder|Supplier newQuery()
 * @method static Builder|Supplier query()
 * @method static Builder|Supplier whereContactName($value)
 * @method static Builder|Supplier whereCreatedAt($value)
 * @method static Builder|Supplier whereCreatedById($value)
 * @method static Builder|Supplier whereEmail($value)
 * @method static Builder|Supplier whereFileId($value)
 * @method static Builder|Supplier whereId($value)
 * @method static Builder|Supplier whereName($value)
 * @method static Builder|Supplier wherePhone($value)
 * @method static Builder|Supplier whereStoreId($value)
 * @method static Builder|Supplier whereSupplierTypeId($value)
 * @method static Builder|Supplier whereUpdatedAt($value)
 * @method static Builder|Supplier whereUpdatedById($value)
 * @method static Builder|Supplier whereWebsite($value)
 * @mixin Eloquent
 * @property-read \App\Models\User|null $created_by
 * @property-read \App\Models\User|null $updated_by
 * @method static Builder|Supplier activeStore(?string $storeId = null)
 */
class Supplier extends Model
{
    use HasFactory;
    use WhoDidIt;
    use ActiveStore;

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function equipments(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function scopeCompanySuppliers(Builder $query): Builder
    {
        return $query->whereIn('company_id', [Auth::user()->company_id, 1]);
    }
}
