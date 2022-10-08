<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\CarCount
 *
 * @property int $id
 * @property int $car_count
 * @property string $store_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|CarCount newModelQuery()
 * @method static Builder|CarCount newQuery()
 * @method static Builder|CarCount query()
 * @method static Builder|CarCount whereCarCount($value)
 * @method static Builder|CarCount whereCreatedAt($value)
 * @method static Builder|CarCount whereCreatedById($value)
 * @method static Builder|CarCount whereId($value)
 * @method static Builder|CarCount whereStoreId($value)
 * @method static Builder|CarCount whereUpdatedAt($value)
 * @method static Builder|CarCount whereUpdatedById($value)
 * @mixin Eloquent
 * @property-read Store|null $store
 * @property-read \App\Models\User|null $created_by
 * @property-read \App\Models\User|null $updated_by
 */
class CarCount extends Model
{
    use HasFactory;
    use WhoDidIt;

    protected static function boot()
    {
        parent::boot();
        static::saved(function (CarCount $carCount) {
            $carCount->store()->update(['current_car_count' => $carCount->car_count]);
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
