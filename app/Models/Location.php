<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Location
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $default_location
 * @property int $position
 * @property string $store_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Equipment[] $equipments
 * @property-read int|null $equipments_count
 * @property-read Store|null $store
 * @method static Builder|Location newModelQuery()
 * @method static Builder|Location newQuery()
 * @method static Builder|Location ordered(string $direction = 'asc')
 * @method static Builder|Location query()
 * @method static Builder|Location storeLocations(string $store_id)
 * @method static Builder|Location whereCreatedAt($value)
 * @method static Builder|Location whereCreatedById($value)
 * @method static Builder|Location whereDefaultLocation($value)
 * @method static Builder|Location whereDescription($value)
 * @method static Builder|Location whereId($value)
 * @method static Builder|Location whereName($value)
 * @method static Builder|Location wherePosition($value)
 * @method static Builder|Location whereStoreId($value)
 * @method static Builder|Location whereUpdatedAt($value)
 * @method static Builder|Location whereUpdatedById($value)
 * @mixin Eloquent
 * @method static Builder|Location defaultLocation(string $storeId)
 * @property-read \App\Models\User|null $created_by
 * @property-read \App\Models\User|null $updated_by
 */
class Location extends Model implements Sortable
{
    use HasFactory;
    use WhoDidIt;
    use SortableTrait;

    protected static function boot()
    {
        parent::boot();
        static::saving(function (Location $location) {
            if ($location->default_location) {
                $oldDefault = Location::where('default_location', true)
                    ->where('store_id', $location->store_id)
                    ->first();
                if ($oldDefault && $oldDefault->id !== $location->id) {
                    $oldDefault->default_location = false;
                    $oldDefault->save();
                }
            }
        });

        static::deleting(function (Location $location) {
            if ($location->default_location) {
                throw new Exception('You can not delete your default location');
            }
        });

        static::deleted(function (Location $location) {
            $equipments = Equipment::where('location_id', $location->id)->get();
            $defaultLocation = Location::where(['default_location' => true, 'store_id' => $location->store_id])->firstOrFail();
            $equipments->each->location()->associate($defaultLocation);
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function equipments(): HasMany
    {
        return $this->hasMany(Equipment::class)->orderBy('position');
    }

    public function scopeStoreLocations(Builder $query, string $store_id): Builder
    {
        return $query->where('store_id', $store_id)->with('equipments');
    }

    public function scopeDefaultLocation(Builder $query, string $storeId): Builder
    {
        return $query->where([
            'store_id' => $storeId,
            'default_location' => true,
        ]);
    }
}
