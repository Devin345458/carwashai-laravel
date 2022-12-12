<?php

namespace App\Models;

use App\Traits\ActiveStore;
use App\Traits\WhoDidIt;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\EquipmentGroup
 *
 * @property int $id
 * @property string $name
 * @property string $store_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Equipment[] $equipments
 * @property-read int|null $equipments_count
 * @property-read Collection|Maintenance[] $maintenances
 * @property-read int|null $maintenances_count
 * @property-read Store|null $store
 * @method static Builder|EquipmentGroup newModelQuery()
 * @method static Builder|EquipmentGroup newQuery()
 * @method static Builder|EquipmentGroup query()
 * @method static Builder|EquipmentGroup whereCreatedAt($value)
 * @method static Builder|EquipmentGroup whereCreatedById($value)
 * @method static Builder|EquipmentGroup whereId($value)
 * @method static Builder|EquipmentGroup whereName($value)
 * @method static Builder|EquipmentGroup whereStoreId($value)
 * @method static Builder|EquipmentGroup whereUpdatedAt($value)
 * @method static Builder|EquipmentGroup whereUpdatedById($value)
 * @mixin Eloquent
 * @property-read User|null $created_by
 * @property-read User|null $updated_by
 * @method static Builder|EquipmentGroup activeStore(?string $storeId = null)
 */
class EquipmentGroup extends Model
{
    use HasFactory;
    use WhoDidIt;
    use ActiveStore;

    protected $fillable = [
        'name',
        'store_id',
        'equipments'
    ];

    public static function booted()
    {
        parent::booted();
        self::deleting(function (EquipmentGroup $equipmentGroup) {
            $equipmentGroup->maintenances()->delete();
        });
    }

    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class);
    }

    public function maintenances(): MorphMany
    {
        return $this->morphMany(Maintenance::class, 'maintainable');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
