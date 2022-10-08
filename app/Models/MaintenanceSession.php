<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\MaintenanceSession
 *
 * @property int $id
 * @property string $start_time
 * @property string|null $end_time
 * @property string $store_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Maintenance[] $maintenances
 * @property-read int|null $maintenances_count
 * @property-read Store|null $store
 * @method static Builder|MaintenanceSession newModelQuery()
 * @method static Builder|MaintenanceSession newQuery()
 * @method static Builder|MaintenanceSession query()
 * @method static Builder|MaintenanceSession whereCreatedAt($value)
 * @method static Builder|MaintenanceSession whereCreatedById($value)
 * @method static Builder|MaintenanceSession whereEndTime($value)
 * @method static Builder|MaintenanceSession whereId($value)
 * @method static Builder|MaintenanceSession whereStartTime($value)
 * @method static Builder|MaintenanceSession whereStoreId($value)
 * @method static Builder|MaintenanceSession whereUpdatedAt($value)
 * @method static Builder|MaintenanceSession whereUpdatedById($value)
 * @mixin Eloquent
 * @property-read User|null $created_by
 * @property-read User|null $updated_by
 */
class MaintenanceSession extends Model
{
    use HasFactory;
    use WhoDidIt;

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function maintenances(): BelongsToMany
    {
        return $this->belongsToMany(Maintenance::class)->withPivot(['complete', 'id'])->using(MaintenanceMaintenanceSession::class);
    }

}
