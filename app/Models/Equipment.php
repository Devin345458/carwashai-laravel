<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Auth;
use BeyondCode\Comments\Comment;
use BeyondCode\Comments\Traits\HasComments;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Znck\Eloquent\Traits\BelongsToThrough;

/**
 * App\Models\Equipment
 *
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Collection|Category[] $categories
 * @property-read int|null $categories_count
 * @property-read int|null $comments_count
 * @property-read File|null $display_image
 * @property-read Collection|EquipmentGroup[] $equipment_groups
 * @property-read int|null $equipment_groups_count
 * @property-read Collection|File[] $files
 * @property-read int|null $files_count
 * @property-read Location|null $location
 * @property-read Collection|Maintenance[] $maintenances
 * @property-read int|null $maintenances_count
 * @property-read Supplier|null $manufacturer
 * @property-read Collection|Repair[] $repairs
 * @property-read int|null $repairs_count
 * @property-read Store|null $store
 * @method static Builder|Equipment activeEquipment(string $store_id = null)
 * @method static Builder|Equipment newModelQuery()
 * @method static Builder|Equipment newQuery()
 * @method static Builder|Equipment ordered(string $direction = 'asc')
 * @method static Builder|Equipment query()
 * @mixin Eloquent
 * @property-read Collection|Comment[] $comments
 * @property int $id
 * @property string $name
 * @property int $file_id
 * @property int $position
 * @property int $location_id
 * @property string $store_id
 * @property int $manufacturer_id
 * @property int $created_from_id
 * @property string $purchase_date
 * @property string $install_date
 * @property string $installer
 * @property string $warranty_expiration
 * @property string $model_number
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Equipment whereCreatedAt($value)
 * @method static Builder|Equipment whereCreatedById($value)
 * @method static Builder|Equipment whereCreatedFromId($value)
 * @method static Builder|Equipment whereFileId($value)
 * @method static Builder|Equipment whereId($value)
 * @method static Builder|Equipment whereInstallDate($value)
 * @method static Builder|Equipment whereInstaller($value)
 * @method static Builder|Equipment whereLocationId($value)
 * @method static Builder|Equipment whereManufacturerId($value)
 * @method static Builder|Equipment whereModelNumber($value)
 * @method static Builder|Equipment whereName($value)
 * @method static Builder|Equipment wherePosition($value)
 * @method static Builder|Equipment wherePurchaseDate($value)
 * @method static Builder|Equipment whereStoreId($value)
 * @method static Builder|Equipment whereUpdatedAt($value)
 * @method static Builder|Equipment whereUpdatedById($value)
 * @method static Builder|Equipment whereWarrantyExpiration($value)
 * @property-read User|null $created_by
 * @property-read User|null $updated_by
 * @property-read Collection|\App\Models\MaintenanceMaintenanceSession[] $completed_maintenances
 * @property-read int|null $completed_maintenances_count
 */
class Equipment extends Model implements Sortable
{
    use HasFactory;
    use WhoDidIt;
    use SortableTrait;
    use LogsActivity;
    use HasComments;
    use BelongsToThrough;

    protected $table = 'equipments';

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function equipment_groups(): BelongsToMany
    {
        return $this->belongsToMany(EquipmentGroup::class);
    }

    public function display_image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class)->withTimestamps();
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'manufacturer_id');
    }

    public function company(): \Znck\Eloquent\Relations\BelongsToThrough
    {
        return $this->belongsToThrough(Company::class, Store::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function maintenances(): MorphMany
    {
        return $this->morphMany(Maintenance::class, 'maintainable');
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class);
    }

    public function completed_maintenances(): HasManyThrough
    {
        return $this->hasManyThrough(MaintenanceMaintenanceSession::class, Maintenance::class, 'maintainable_id')
            ->where('maintainable_type', array_search(static::class, Relation::morphMap()) ?: static::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->setDescriptionForEvent(function ($eventName) {
                return Auth::user()->full_name . ' ' .$eventName . ' equipment ' . $this->name;
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    public function scopeActiveEquipment(Builder $query, $store_id): Builder
    {
        if (!$store_id) {
            $query
                ->ordered()
                ->whereHas('store.users', function (Builder $query) {
                    return $query->where('users.id', Auth::id());
                });
        } else {
            $query
                ->where('store_id', $store_id)
                ->ordered();
        }

        return $query;
    }
}
