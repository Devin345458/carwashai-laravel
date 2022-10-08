<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Auth;
use Carbon\Carbon;
use DB;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Maintenance
 *
 * @property int $id
 * @property string $name
 * @property string $method
 * @property int $expected_duration
 * @property int $frequency_days
 * @property int $frequency_cars
 * @property int $file_id
 * @property string $maintainable_type
 * @property int $maintainable_id
 * @property string $store_id
 * @property string $last_completed_date
 * @property int $last_cars_completed
 * @property string $procedures
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Collection|Item[] $consumables
 * @property-read int|null $consumables_count
 * @property-read File|null $file
 * @property-read Collection|Item[] $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent $maintainable
 * @property-read Collection|MaintenanceSession[] $maintenance_sessions
 * @property-read int|null $maintenance_sessions_count
 * @property-read Collection|Item[] $parts
 * @property-read int|null $parts_count
 * @property-read Collection|Repair[] $repairs
 * @property-read int|null $repairs_count
 * @property-read Store|null $store
 * @property-read Collection|Item[] $tools
 * @property-read int|null $tools_count
 * @method static Builder|Maintenance due(string $store_id, bool $due)
 * @method static Builder|Maintenance newModelQuery()
 * @method static Builder|Maintenance newQuery()
 * @method static \Illuminate\Database\Query\Builder|Maintenance onlyTrashed()
 * @method static Builder|Maintenance query()
 * @method static Builder|Maintenance whereCreatedAt($value)
 * @method static Builder|Maintenance whereCreatedById($value)
 * @method static Builder|Maintenance whereDeletedAt($value)
 * @method static Builder|Maintenance whereExpectedDuration($value)
 * @method static Builder|Maintenance whereFileId($value)
 * @method static Builder|Maintenance whereFrequencyCars($value)
 * @method static Builder|Maintenance whereFrequencyDays($value)
 * @method static Builder|Maintenance whereId($value)
 * @method static Builder|Maintenance whereLastCarsCompleted($value)
 * @method static Builder|Maintenance whereLastCompletedDate($value)
 * @method static Builder|Maintenance whereMaintainableId($value)
 * @method static Builder|Maintenance whereMaintainableType($value)
 * @method static Builder|Maintenance whereMethod($value)
 * @method static Builder|Maintenance whereName($value)
 * @method static Builder|Maintenance whereProcedures($value)
 * @method static Builder|Maintenance whereStoreId($value)
 * @method static Builder|Maintenance whereUpdatedAt($value)
 * @method static Builder|Maintenance whereUpdatedById($value)
 * @method static \Illuminate\Database\Query\Builder|Maintenance withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Maintenance withoutTrashed()
 * @mixin Eloquent
 * @property-read User|null $created_by
 * @property-read User|null $updated_by
 * @property-read Collection|\App\Models\MaintenanceSession[] $completed_maintenances
 * @property-read int|null $completed_maintenances_count
 */
class Maintenance extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WhoDidIt;
    use LogsActivity;

    protected $fillable = [
        'name',
        'method',
        'expected_duration',
        'frequency_days',
        'frequency_cars',
        'file_id',
        'maintainable_type',
        'maintainable_id',
        'store_id',
        'last_completed_date',
        'last_cars_completed',
        'procedures',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(function (Maintenance $maintenance) {
            if ($maintenance->frequency_days) {
                $maintenance->method = 'Time';
            } else {
                $maintenance->method = 'Car Count';
            }
        });

        static::creating(function (Maintenance $maintenance) {
            if ($maintenance->frequency_days) {
                $maintenance->frequency_cars = 0;
                $maintenance->last_completed_date = Carbon::now();
            } else {
                $current_car_count = CarCount::where('store_id', $maintenance->store_id)->sum('car_count');
                $maintenance->last_cars_completed = $current_car_count;
            }
        });
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function maintainable(): MorphTo
    {
        return $this->morphTo();
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withPivot('quantity');
    }

    public function parts(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->where('item_type_id', 1)->withPivot('quantity');
    }

    public function consumables(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->where('item_type_id', 2)->withPivot('quantity');
    }

    public function tools(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->where('item_type_id', 3)->withPivot('quantity');
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class);
    }

    public function maintenance_sessions(): BelongsToMany
    {
        return $this->belongsToMany(MaintenanceSession::class)->using(MaintenanceMaintenanceSession::class);
    }

    public function completed_maintenances(): BelongsToMany
    {
        return $this->belongsToMany(MaintenanceSession::class)
            ->using(MaintenanceMaintenanceSession::class)
            ->where('complete', '=', true);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(function ($eventName) {
                return Auth::user()->full_name . ' ' .$eventName . ' maintenance ' . $this->name . ' for ' . $this->maintainable->name;
            })
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    /**
     * Update due dates for maintenance
     *
     * @param  Maintenance $maintenance The maintenance
     */
    public static function updateMaintenanceDueDate(Maintenance $maintenance)
    {
        $current_car_count = Store::findOrFail($maintenance->store_id)->current_car_count;
        $maintenance->last_cars_completed = $current_car_count;
        $maintenance->last_completed_date = Carbon::now();
        $maintenance->save();
    }

    /**
     * Complete a maintenance
     *
     * @param int $maintenance_id The maintenance to complete
     * @return void
     * @throws Exception
     */
    public static function complete(int $maintenance_id): void
    {
        $maintenance = Maintenance::findOrFail($maintenance_id)->load('parts');

        foreach ($maintenance->parts as $item) {
            Inventory::use($item, $item->pivot->quantity, $maintenance->store_id, 5);
        }

        Maintenance::updateMaintenanceDueDate($maintenance);
    }

    /**
     * Returns store maintenance grouped by location and grouped equipment and sorted by equipment order
     *
     * @param string|null $store_id The store id
     * @param bool $due Whether to get due or upcoming
     * @return Location[]
     */
    public static function dueEquipmentMaintenance(?string $storeId, bool $due): array
    {
        $maintenances = static::due($storeId, $due)
            ->with([
                'maintainable',
                'items.inventories' => function (HasMany $q) use ($storeId) {
                    return $q->where(['inventories.store_id' => $storeId]);
                },
            ])
            ->get();

        return $maintenances
            ->map(function (Maintenance $maintenance) {
                switch ($maintenance->maintainable_type) {
                    case 'App\Models\Equipment':
                        $maintenance->maintainable->load('location');
                        break;
                    case 'App\Models\EquipmentGroup':
                        $maintenance->maintainable->load('equipments.location');
                        $location = $maintenance->maintainable->equipments
                            ->map(function (Equipment $equipment) {
                                return $equipment->location;
                            })
                            ->sortBy('position', SORT_ASC)->first();

                        $position = collect($maintenance->maintainable->equipments)
                            ->filter(function (Equipment $equipment) use ($location) {
                                return $equipment->location_id === $location->id;
                            })
                            ->sortBy('position', SORT_ASC)
                            ->first()
                            ->position;

                        $maintenance->maintainable->location = $location;
                        $maintenance->maintainable->position = $position;
                        break;
                    default:
                        throw new Exception('Invalid Association');
                }
                return $maintenance;
            })
            ->sortBy('maintainable.position', SORT_ASC)
            ->sortBy('maintainable.location.position', SORT_ASC)
            ->values()
            ->toArray();
    }

    /**
     * Find due or upcoming maintenance with store settings offset
     *
     * @param Builder $q The query
     * @return Builder
     */
    public function scopeDue(Builder $q, ?string $storeId, bool $due): Builder
    {
        $q->select('maintenances.*');
        $q->join('stores', function (JoinClause $query) use ($storeId) {
            $query->on('stores.id', '=', 'maintenances.store_id');
            $query->when($storeId, function ($query, $storeId) {
               $query->where('stores.id', '=', $storeId);
            });
        });

        if ($due) {
           $q->where(DB::raw('DATE_ADD(maintenances.last_completed_date, INTERVAL maintenances.frequency_days DAY)'), '<=', DB::raw('CURRENT_TIMESTAMP() + (stores.maintenance_due_days_offset * 86400000)'));
        } else {
            $q->where(DB::raw('DATE_ADD(maintenances.last_completed_date, INTERVAL maintenances.frequency_days DAY)'), '<=', DB::raw('CURRENT_TIMESTAMP() + (stores.upcoming_days_offset * 86400000)'));
            $q->orWhere(DB::raw('DATE_ADD(maintenances.last_completed_date, INTERVAL maintenances.frequency_days DAY)'), '>', DB::raw('CURRENT_TIMESTAMP() + (stores.maintenance_due_days_offset * 86400000)'));
        }

        return $q;
    }
}
