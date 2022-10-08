<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\CompletedInventory
 *
 * @property int $id
 * @property int $time_to_complete
 * @property int $item_count
 * @property int $item_skip_count
 * @property string $store_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Store|null $store
 * @method static Builder|CompletedInventory newModelQuery()
 * @method static Builder|CompletedInventory newQuery()
 * @method static Builder|CompletedInventory query()
 * @method static Builder|CompletedInventory whereCreatedAt($value)
 * @method static Builder|CompletedInventory whereCreatedById($value)
 * @method static Builder|CompletedInventory whereId($value)
 * @method static Builder|CompletedInventory whereItemCount($value)
 * @method static Builder|CompletedInventory whereItemSkipCount($value)
 * @method static Builder|CompletedInventory whereStoreId($value)
 * @method static Builder|CompletedInventory whereTimeToComplete($value)
 * @method static Builder|CompletedInventory whereUpdatedAt($value)
 * @method static Builder|CompletedInventory whereUpdatedById($value)
 * @mixin Eloquent
 * @property-read \App\Models\User|null $created_by
 * @property-read \App\Models\User|null $updated_by
 */
class CompletedInventory extends Model
{
    use HasFactory;
    use WhoDidIt;
    use LogsActivity;

    public function store() {
        return $this->belongsTo(Store::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(function () {
                return Auth::user()->full_name . ' conducted an inventory';
            })
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
}
