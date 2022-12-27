<?php

namespace App\Models;

use App\Traits\ActiveStore;
use App\Traits\WhoDidIt;
use Arr;
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
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Str;

/**
 * App\Models\Repair
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $time
 * @property string $due_date
 * @property int $priority
 * @property float $health_impact
 * @property string $status
 * @property string $solution
 * @property string $store_id
 * @property int $equipment_id
 * @property int $assigned_to_id
 * @property int $assigned_by_id
 * @property string $assigned_date
 * @property int $maintenance_id
 * @property int $repair_id
 * @property int $completed
 * @property string $completed_reason
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read User|null $assigned_by
 * @property-read User|null $assigned_to
 * @property-read int|null $comments_count
 * @property-read Equipment|null $equipment
 * @property-read Collection|File[] $files
 * @property-read int|null $files_count
 * @property-read Collection|Item[] $items
 * @property-read int|null $items_count
 * @property-read Maintenance|null $maintenance
 * @property-read RepairReminder|null $repair_reminder
 * @property-read Collection|RepairReminder[] $repair_reminders
 * @property-read int|null $repair_reminders_count
 * @property-read Store|null $store
 * @method static Builder|Repair dashboard()
 * @method static Builder|Repair newModelQuery()
 * @method static Builder|Repair newQuery()
 * @method static Builder|Repair query()
 * @method static Builder|Repair repairs(array $assigned_to_ids = [], array $assigned_by_ids = [], array $created_by_ids = [], array $equipment_ids = [], array $statuses = [], ?string $search = null, $priority = -1)
 * @method static Builder|Repair whereAssignedById($value)
 * @method static Builder|Repair whereAssignedDate($value)
 * @method static Builder|Repair whereAssignedToId($value)
 * @method static Builder|Repair whereCompleted($value)
 * @method static Builder|Repair whereCompletedReason($value)
 * @method static Builder|Repair whereCreatedAt($value)
 * @method static Builder|Repair whereCreatedById($value)
 * @method static Builder|Repair whereDescription($value)
 * @method static Builder|Repair whereDueDate($value)
 * @method static Builder|Repair whereEquipmentId($value)
 * @method static Builder|Repair whereHealthImpact($value)
 * @method static Builder|Repair whereId($value)
 * @method static Builder|Repair whereMaintenanceId($value)
 * @method static Builder|Repair whereName($value)
 * @method static Builder|Repair wherePriority($value)
 * @method static Builder|Repair whereRepairId($value)
 * @method static Builder|Repair whereSolution($value)
 * @method static Builder|Repair whereStatus($value)
 * @method static Builder|Repair whereStoreId($value)
 * @method static Builder|Repair whereTime($value)
 * @method static Builder|Repair whereUpdatedAt($value)
 * @method static Builder|Repair whereUpdatedById($value)
 * @mixin Eloquent
 * @property-read Collection|Comment[] $comments
 * @property-read User|null $created_by
 * @property-read User|null $updated_by
 * @method static Builder|Repair activeStore(?string $storeId = null)
 * @method static Builder|Repair repairsFiltered(array $assigned_to_ids = [], array $assigned_by_ids = [], array $created_by_ids = [], array $equipment_ids = [], array $statuses = [], ?string $search = null, $priority = -1)
 * @method static Builder|Repair repairsTable()
 * @property string $type
 * @method static Builder|Repair whereType($value)
 * @property string $findable_type
 * @property int $findable_id
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $findable
 * @method static Builder|Repair whereFindableId($value)
 * @method static Builder|Repair whereFindableType($value)
 */
class Repair extends Model
{
    use HasFactory;
    use WhoDidIt;
    use LogsActivity;
    use HasComments;
    use ActiveStore;

    public const STATUS_COMPLETE = 'Complete';
    public const STATUS_PENDING = 'Pending Assignment';
    public const STATUS_IN_PROGRESS = 'In Progress';
    public const STATUS_ASSIGNED = 'Assigned';
    public const STATUS_SCHEDULED = 'Scheduled';
    public const STATUS_WAITING = 'Waiting';
    public const STATUS_MONITORING = 'Monitoring';

    public const TYPE_REPAIR = 'repair';

    protected static function boot()
    {
        parent::boot();
        static::saving(function (Repair $repair) {
            if (!$repair->priority) {
                $repair->priority = 0;
            }

            if (!$repair->status) {
                $repair->status = 'Pending Assignment';
            }
        });
    }

    public function assigned_to(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assigned_by(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function findable(): MorphTo
    {
        return $this->morphTo();
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withPivot('quantity');
    }

    public function repair_reminder(): HasOne
    {
        return $this->hasOne(RepairReminder::class)->where('user_id', Auth::id());
    }

    public function repair_reminders(): HasMany
    {
        return $this->hasMany(RepairReminder::class);
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->setDescriptionForEvent(function ($eventName) {
                $description = Auth::user()->full_name . ' ' .$eventName . ' task ' . $this->name;
                if ($eventName === 'updated') {
                    $dirty = $this->getDirty();
                    unset($dirty['updated_at']);
                    if (Arr::exists($dirty, 'assigned_to_id')) {
                        if ($dirty['assigned_to_id']) {
                            $description = Auth::user()->full_name . ' assigned task to "' . $this->assigned_to->full_name . '"';
                        } else {
                            $description = Auth::user()->full_name . ' unassigned task';
                        }
                    } else if (isset($dirty['status'])) {
                        if ($this->status === Repair::STATUS_COMPLETE) {
                            $description = Auth::user()->full_name . ' completed task ' . $this->name;
                        } else {
                            $description = Auth::user()->full_name . ' changed the status ' . $this->name .  ' to ' . Str::title($this->status);
                        }
                    } else {
                        $description = Auth::user()->full_name . ' updated ' . count($dirty) . ' fields to ' . $this->name;
                    }
                }

                return $description;
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }


    public function scopeRepairsFiltered(Builder $query, array $assigned_to_ids = [], array $assigned_by_ids = [], array $created_by_ids = [], array $equipment_ids = [], array $statuses = [], array $types = [], string $search = null, $priority = -1)
    {
        /** @var Repair $query */
        $query->repairsTable();

        $query->when($assigned_to_ids, function (Builder $query, $assigned_to_ids) {
            return $query->whereIn('repairs.assigned_to_id', $assigned_to_ids);
        });

        $query->when($assigned_by_ids, function (Builder $query, $assigned_by_ids) {
            return $query->whereIn('repairs.assigned_by_id', $assigned_by_ids);
        });

        $query->when($created_by_ids, function (Builder $query, $created_by_ids) {
            return $query->whereIn('repairs.created_by_id', $created_by_ids);
        });

        $query->when($equipment_ids, function (Builder $query, $equipment_ids) {
            return $query->whereIn('repairs.equipment_id', $equipment_ids);
        });

        $query->when($types, function (Builder $query, $types) {
            return $query->whereIn('repairs.type', $types);
        });

        if ($statuses) {
            $query->whereIn('repairs.status', $statuses);
        } else {
            $query->where('repairs.status', '<>', Repair::STATUS_COMPLETE);
        }

        $query->when($search, function (Builder $query, $search) {
            return $query->where('repairs.name', 'LIKE', '%' . $search . '%');
        });

        $query->when($priority && $priority !== -1, function (Builder $query) use ($priority) {
            return $query->where('repairs.name', $priority);
        });
    }

    public function scopeRepairsTable(Builder $query): Builder
    {
        return $query->with([
            'equipment' => function (BelongsTo $query) {
                $query->select([
                    'id',
                    'name',
                    'file_id'
                ]);
            },
            'store' => function (BelongsTo $query) {
                $query->select([
                    'id',
                    'name',
                    'file_id'
                ]);
            },
            'assigned_to' => function (BelongsTo $query) {
                $query->select([
                    'id',
                    'first_name',
                    'last_name',
                    'file_id'
                ]);
            }
        ])
            ->withCount([
                'comments',
                'files'
            ]);
    }

    public function scopeDashboard(Builder $query)
    {
        return $query->repairsFiltered()->where('repairs.completed', false);
    }
}
