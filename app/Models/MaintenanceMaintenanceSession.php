<?php

namespace App\Models;

use BeyondCode\Comments\Comment;
use BeyondCode\Comments\Traits\HasComments;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * App\Models\MaintenanceMaintenanceSession
 *
 * @property int $id
 * @property int $maintenance_id
 * @property int $maintenance_session_id
 * @property int $complete
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Comment[] $comments
 * @property-read int|null $comments_count
 * @method static Builder|MaintenanceMaintenanceSession newModelQuery()
 * @method static Builder|MaintenanceMaintenanceSession newQuery()
 * @method static Builder|MaintenanceMaintenanceSession query()
 * @method static Builder|MaintenanceMaintenanceSession whereComplete($value)
 * @method static Builder|MaintenanceMaintenanceSession whereCreatedAt($value)
 * @method static Builder|MaintenanceMaintenanceSession whereId($value)
 * @method static Builder|MaintenanceMaintenanceSession whereMaintenanceId($value)
 * @method static Builder|MaintenanceMaintenanceSession whereMaintenanceSessionId($value)
 * @method static Builder|MaintenanceMaintenanceSession whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read \App\Models\Maintenance|null $maintenance
 */
class MaintenanceMaintenanceSession extends Pivot
{
    use HasFactory;
    use HasComments;

    public $incrementing = true;
    public $timestamps = true;

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class);
    }
}
